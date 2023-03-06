<?php

namespace App\Models;

use App\Helpers\Entity\FieldsMapping;
use App\Http\Traits\HasBreadcrumbs;
use App\Http\Traits\SearchableEntity;
use App\Models\Contracts\EntityContract;
use App\Models\Traits\ReviewAndAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FundraisingRound extends Model implements EntityContract
{
    use HasFactory,
        LogsActivity,
        SearchableEntity,
        HasBreadcrumbs,
        ReviewAndAudit;

    protected $guarded = ['id'];

    const ROUND_SEED = 'Seed';
    const ROUND_SERIES_A = 'Series A';
    const ROUND_SERIES_B = 'Series B';
    const ROUND_SERIES_C = 'Series C';
    const ROUND_SERIES_D = 'Series D';
    const ROUND_UNKNOWN = 'Unknown';
    const ROUND_PRE_SEED = 'Pre-seed';
    const ROUND_DEBT = 'Debt';
    const ROUND_GRANTS = 'Grants';
    const ROUND_IPO = 'IPO';
    const ROUND_POST_IPO = 'Post-IPO Equity';

    const ROUNDS = [
        self::ROUND_PRE_SEED,
        self::ROUND_SEED,
        self::ROUND_SERIES_A,
        self::ROUND_SERIES_B,
        self::ROUND_SERIES_C,
        self::ROUND_SERIES_D,
        self::ROUND_UNKNOWN,
        self::ROUND_DEBT,
        self::ROUND_GRANTS,
        self::ROUND_IPO,
        self::ROUND_POST_IPO
    ];

    private string $searchableRouteName = 'admin.fundraising-rounds.show';
    private string $searchableRouteField = 'id';
    private string $searchableIcon = 'fa-duotone fa-sack-dollar';

    public static $routeParameterField = 'id';

    /**
     * Relationships
     */
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Investor::class)->withTimestamps();
    }

    /**
     * Functions
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_FUNDRAISING_ROUNDS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    public static function getFieldsMapping()
    {
        return [
            //attributes
            'name' => [
                'type' => FieldsMapping::TYPE_STRING,
                'required' => true,
            ],
            'round' => [
                'type' => FieldsMapping::TYPE_ENUM,
                'values' => self::ROUNDS,
                'required' => true,
            ],
            'date' => [
                'type' => FieldsMapping::TYPE_DATE,
                'required' => true,
            ],
            'amount_raised' => [
                'type' => FieldsMapping::TYPE_FLOAT,
            ],
            'announcement_url' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'description' => [
                'type' => FieldsMapping::TYPE_TEXT,
            ],
            'valuation' => [
                'type' => FieldsMapping::TYPE_FLOAT,
            ],
            //relations
            'organization' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_ONE_ONE,
                'relationField' => 'name',
            ],
            'investors' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
            ],
        ];
    }
}
