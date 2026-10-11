<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * DEV-15. Lists the marketplace listings whose address is outside the United States, so someone can review them (for
 * example listings in France). STRICTLY READ-ONLY: it never changes, hides or deletes a row. It only reads, prints a table
 * and, when asked, writes a CSV file under storage/app.
 *
 * A listing is looked at through its primary address (or its first address when none is marked primary). It is listed when
 * that address's country is not the US, or is clearly France. A listing with no address, or with an address that has no
 * country at all, cannot be placed, so it is not listed; the command says how many it could not check.
 */
class ListingsReportNonUs extends Command
{
    private const US_VALUES = ['us', 'usa', 'u.s.', 'u.s.a.', 'united states', 'united states of america'];

    private const FRANCE_VALUES = ['fr', 'fra', 'france', 'republique francaise', 'french republic'];

    protected $signature = 'listings:report-non-us
        {--csv= : Also write the list to this CSV file, a path under storage/app (for example reports/non-us-listings.csv)}';

    protected $description = 'Read-only: list marketplace listings whose address is outside the US (or in France), newest first';

    public function handle(): int
    {
        $csvPath = $this->option('csv') ? trim((string) $this->option('csv')) : null;

        if ($csvPath !== null && ! $this->isSafeCsvPath($csvPath)) {
            $this->error('The --csv path must be a relative .csv path under storage/app, with no "..".');

            return self::FAILURE;
        }

        $rows = [];
        $unplaced = 0;

        // Read in chunks by id so memory stays small; the list is put newest first below.
        Listing::query()
            ->with('addresses')
            ->chunkById(200, function ($listings) use (&$rows, &$unplaced) {
                foreach ($listings as $listing) {
                    $address = $listing->addresses->firstWhere('primary', 1) ?? $listing->addresses->first();

                    if (! $address || ! $this->hasCountry($address)) {
                        $unplaced++;

                        continue;
                    }

                    if ($this->isNonUs($address)) {
                        $rows[] = [
                            'id' => $listing->id,
                            'name' => $listing->name,
                            'type' => $listing->type,
                            'status' => $listing->status,
                            'city' => (string) $address->city,
                            'country' => $this->countryLabel($address),
                            'url' => $listing->show_url,
                            'created_at' => (string) $listing->created_at,
                        ];
                    }
                }
            }, 'id');

        // Newest first; the id breaks ties so the order is stable.
        usort($rows, fn ($a, $b) => [$b['created_at'], $b['id']] <=> [$a['created_at'], $a['id']]);

        if ($rows === []) {
            $this->line('No listing with a non-US address was found.');
        } else {
            $this->table(
                ['ID', 'Name', 'Type', 'Status', 'City', 'Country', 'Profile URL'],
                array_map(fn ($row) => [$row['id'], $row['name'], $row['type'], $row['status'], $row['city'], $row['country'], $row['url']], $rows),
            );
        }

        $this->line(sprintf('%d listing(s) with a non-US address (%d in France).', count($rows), count(array_filter($rows, fn ($row) => $this->looksFrench($row['country'])))));
        $this->line(sprintf('%d listing(s) have no address or no country and could not be checked.', $unplaced));
        $this->line('Nothing was changed.');

        if ($csvPath !== null) {
            Storage::disk('local')->put($csvPath, $this->csv($rows));
            $this->line('CSV written to storage/app/' . $csvPath);
        }

        return self::SUCCESS;
    }

    private function hasCountry($address): bool
    {
        return trim((string) $address->country_code) !== '' || trim((string) $address->country) !== '';
    }

    private function isNonUs($address): bool
    {
        $code = $this->normalize((string) $address->country_code);
        $country = $this->normalize((string) $address->country);

        $isUs = in_array($code, self::US_VALUES, true) || in_array($country, self::US_VALUES, true);
        $isFrance = in_array($code, self::FRANCE_VALUES, true) || in_array($country, self::FRANCE_VALUES, true);

        return $isFrance || ! $isUs;
    }

    private function looksFrench(string $label): bool
    {
        return in_array($this->normalize($label), self::FRANCE_VALUES, true);
    }

    private function countryLabel($address): string
    {
        $country = trim((string) $address->country);

        return $country !== '' ? $country : strtoupper(trim((string) $address->country_code));
    }

    /** Lower case, no accents, no surrounding space: "Français" style differences do not matter. */
    private function normalize(string $value): string
    {
        return strtolower(trim(preg_replace('/[^\x20-\x7E]/', '', \Illuminate\Support\Str::ascii($value))));
    }

    private function isSafeCsvPath(string $path): bool
    {
        return preg_match('/\.csv$/i', $path) === 1
            && ! str_starts_with($path, '/')
            && ! str_contains($path, '..')
            && ! str_contains($path, '\\');
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function csv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['id', 'name', 'type', 'status', 'city', 'country', 'profile_url', 'created_at']);

        foreach ($rows as $row) {
            // A cell that starts with = + - or @ would be run as a formula by a spreadsheet; a leading quote makes it text.
            fputcsv($handle, array_map(
                fn ($cell) => is_string($cell) && preg_match('/^[=+\-@]/', $cell) ? "'" . $cell : $cell,
                [$row['id'], $row['name'], $row['type'], $row['status'], $row['city'], $row['country'], $row['url'], $row['created_at']],
            ));
        }

        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
