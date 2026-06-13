<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'admin_activity_logs';
    protected $primaryKey = '_id';

    protected $guarded = [];
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'changes' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $with = ['admin'];
    public $timestamps = true;

    /**
     * Get the admin who performed the action
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope: Get logs by entity type
     */
    public function scopeByEntity($query, $entityType, $entityId = null)
    {
        if ($entityId) {
            return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
        }
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope: Get logs by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs by admin
     */
    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope: Get recent logs
     */
    public function scopeRecent($query, $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: Get logs from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute()
    {
        $actions = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'soft_delete' => 'Archived',
            'restore' => 'Restored',
            'approve' => 'Approved',
            'reject' => 'Rejected',
            'flag' => 'Flagged',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get what changed
     */
    public function getChangedFieldsAttribute()
    {
        if (!$this->changes) {
            return [];
        }

        return array_keys(is_array($this->changes) ? $this->changes : []);
    }

    /**
     * Log an action
     *
     * @param string $action
     * @param string $entityType
     * @param mixed $entityId
     * @param array $oldValues
     * @param array $newValues
     * @param string $adminId
     */
    public static function log(
        $action,
        $entityType,
        $entityId,
        $oldValues = null,
        $newValues = null,
        $adminId = null
    ) {
        $logData = [
            'admin_id' => $adminId ?? auth('admin')->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ];

        return \App\Jobs\ProcessAdminActivityLog::dispatch($logData)->afterResponse();
    }
}
