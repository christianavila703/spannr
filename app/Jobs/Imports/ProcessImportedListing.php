<?php

namespace App\Jobs\Imports;

use App\Models\ImportResult;
use App\Models\Listing;
use App\Models\Tag;
use App\Models\TagSet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

class ProcessImportedListing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ImportResult $importResult;

    public function __construct(ImportResult $importResult)
    {
        $this->importResult = $importResult;
    }

    public function handle()
    {
        $recordData = $this->importResult->data;
        $attributes = $this->getAttributes($recordData);

        try {
            $listing = Listing::updateOrCreate(
                ['name' => $recordData['name']],
                $attributes
            );

            $this->importResult->entity_type = Listing::class;
            $this->importResult->entity_id = $listing->id;
            $this->importResult->save();

            $tagErrors = $this->processTreatments($recordData, $listing);
            $addressErrors = $this->processAddress($recordData, $listing);
            $emailErrors = $this->processEmails($recordData, $listing);
            $this->launchCategories($listing);

            $this->saveErrorsAndUpdateStatus($tagErrors, $addressErrors, $emailErrors);

        } catch (Throwable $exception) {
            $this->importResult->status = ImportResult::STATUS_FAILED;
            $this->importResult->exception_errors = [$exception->getMessage()];
            $this->importResult->save();
        }
    }

    private function optionalFields(): array
    {
        return [
            'slug',
            'status',
            'phone',
            'summary',
            'description',
            'insurance',
            'virtual',
            'affiliate_link',
        ];
    }

    private function getAttributes($recordData): array
    {
        $attributes = [
            'name' => $recordData['name'],
            'website' => $recordData['website'],
            'type' => $recordData['type'],
        ];

        $optionalFields = $this->optionalFields();

        foreach($optionalFields as $optional) {
            if (isset($recordData[$optional])) {
                $attributes[$optional] = $recordData[$optional];
            }
        }

        return $attributes;
    }

    private function processTreatments($recordData, $listing): array
    {
        $errors = [];
        $treatmentTag = Tag::where('name', Tag::TAG_TREATMENTS)->first();
        $tagSet = TagSet::where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS)->first();
        $createdTags = [];

        if (isset($recordData['treatments'])) {
            $treatmentValues = explode(',', $recordData['treatments']);

            foreach($treatmentValues as $value) {
                $value = trim($value);
                $matchingTag = Tag::where('name', $value)->first();
                if ($matchingTag) {
                    $listing->treatments()->syncWithoutDetaching([$matchingTag->id]);
                } else {
                    try {
                        $tag = Tag::create([
                            'name' => $value,
                            'parent_id' => $treatmentTag->id,
                            'depth' => 1
                        ]);

                        $tag->tagSets()->syncWithoutDetaching([$tagSet->id]);
                        $listing->treatments()->syncWithoutDetaching([$tag->id]);
                        $createdTags[$tag->id] = $tag->name;
                    } catch (Throwable $exception) {
                        $errors[] = $value . ' ' . $exception->getMessage();
                    }
                }
            }
        }

        if (!empty($createdTags)) {
            $previousErrors = $this->importResult->parent->tag_errors;

            if (empty($previousErrors)) {
                $this->importResult->parent->tag_errors = $createdTags;
            } else {
                $tagErrors = array_merge($previousErrors, $createdTags);
                $this->importResult->parent->tag_errors = $tagErrors;
            }

            $this->importResult->parent->save();
        }

        return $errors;
    }

    private function processEmails($recordData, $listing): array
    {
        $errors = [];

        if(isset($recordData['emails'])) {
            $emails = [];
            $emailData = explode(',', $recordData['emails']);

            foreach($emailData as $email) {
                if(str_contains($email, '@')) {
                    $emails[] = trim($email);
                } else {
                    $errors[] = $email;
                }
            }

            if(!empty($emails)) {
                $listing->emails = $emails;
                $listing->save();
            }
        }

        return $errors;
    }

    private function processAddress($recordData, $listing): array
    {
        $errors = [];

        $listing->addresses()->delete();

        if(isset($recordData['address'])) {
            $address = $recordData['address'];
            $addressParts = explode(',', $address);

            if (count($addressParts) > 3) {
                $errors = ['Check Suite'];
            }

            if (str_contains(strtolower($address), 'suite')) {
                $errors = ['Check Suite'];
            }

            $url = "https://api.geoapify.com/v1/geocode/autocomplete?text={$address}&apiKey=" . config('services.geoapify.key');

            try {
                $request = Http::get($url);
                $response = json_decode($request->body(), true);

                if (array_key_exists('features', $response)) {
                    $addressMatch = $response['features'][0];

                    if ($addressMatch['properties']['rank']['match_type'] === 'full_match') {
                        $name = $addressMatch['properties']['formatted'];
                        $name = str_replace(', United States of America', '', $name);

                        $attributes = [
                            'name' => $name,
                            'street' => $addressParts[0],
                            'city' => $addressMatch['properties']['city'] ?? $addressMatch['properties']['suburb'],
                            'state' => $addressMatch['properties']['state'],
                            'state_code' => $addressMatch['properties']['state_code'],
                            'country' => $addressMatch['properties']['country'],
                            'country_code' => $addressMatch['properties']['country_code'],
                            'postal_code' => $addressMatch['properties']['postcode'],
                            'latitude' => $addressMatch['properties']['lat'],
                            'longitude' => $addressMatch['properties']['lon'],
                            'primary' => 1
                        ];

                        $listing->addresses()->create($attributes);

                    } else {
                        $errors[] = $addressMatch;
                    }
                }

            } catch(Throwable $exception) {
                $errors[] = 'Exception - ' . $exception->getMessage();
            }
        }

         return $errors;
    }

    private function launchCategories($listing) {
        $cold_plunge = $this->importResult->data['cold_plunge'] ?? NULL;
        $sauna = $this->importResult->data['sauna'] ?? NULL;
        $red_light_therapy = $this->importResult->data['red_light_therapy'] ?? NULL;

        if($cold_plunge !== NULL) {
            $tags = Tag::where('name', Listing::SOFT_LAUNCH_COLD_PLUNGE)->pluck('id')->toArray();
            $listing->treatments()->syncWithoutDetaching($tags);
        }

        if($sauna !== NULL) {
            $tags = Tag::where('name', Listing::SOFT_LAUNCH_RED_LIGHT_THERAPY)->pluck('id')->toArray();
            $listing->treatments()->syncWithoutDetaching($tags);
        }

        if($red_light_therapy !== NULL) {
            $tags = Tag::where('name', Listing::SOFT_LAUNCH_SAUNA)->pluck('id')->toArray();
            $listing->treatments()->syncWithoutDetaching($tags);
        }
    }

    private function saveErrorsAndUpdateStatus($tagErrors, $addressErrors, $emailErrors)
    {
        if(!empty($tagErrors)) { $this->importResult->tag_errors = $tagErrors; }
        if(!empty($addressErrors)) { $this->importResult->address_errors = $addressErrors; }
        if(!empty($emailErrors)) { $this->importResult->email_errors = $emailErrors; }

        if(!empty($tagErrors) OR !empty($addressErrors) OR !empty($emailErrors)) {
            $this->importResult->status = ImportResult::STATUS_HAS_ERRORS;
        } else {
            $this->importResult->status = ImportResult::STATUS_COMPLETED;
        }

        $this->importResult->save();
    }
}
