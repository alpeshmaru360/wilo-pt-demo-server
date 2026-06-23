<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB; // A Code: 01-04-2026

class ControlPanelsMaster extends Model
{
    protected $table = 'control_panels_master';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'no_of_pumps',
        'power_rating',
        'power_supply',
        'applications',
        'min_of_ambient_temp',
        'starter_type',
        'communication_protocol',
        'ip_rating',
        'components',
        'enclosure',
        'range',
        'folders_name',
        'file_name_comes_under_this_folder',
        'table_name',
        'code',
    ];

    // A Code: 16-06-2026 Start
    /**
     * Control Panel Master Details
     */
    // public function details()
    // {
    //     return $this->hasMany(
    //         ControlPanelsMasterDetail::class,
    //         'control_panel_master_id',
    //         'id'
    //     );
    // }
    // A Code: 16-06-2026 End

    // A Code: 17-06-2026 Start
    public function getPowerRatingArrayAttribute()
    {
        return array_map('trim', explode(',', $this->power_rating));
    }

    public function getPowerSupplyArrayAttribute()
    {
        return array_map('trim', explode(',', $this->power_supply));
    }

    public function getApplicationsArrayAttribute()
    {
        return array_map('trim', explode(',', $this->applications));
    }
    // A Code: 17-06-2026 End

    // A Code: 01-04-2026 Start
    public static function isIntegerColumn($power) {
        $integerColumn = array(3, 4, 9, 11, 15, 22, 30, 37, 45, 55, 75);
        if (in_array($power, $integerColumn)) {
            return true;
        }
        return false;
    }

    public static function controlpanel_over_head() {
        return DB::table('setup_fields')->where('name', 'controlpanel_over_head')->pluck('value')[0];
    }
    // A Code: 01-04-2026 End
    
}
