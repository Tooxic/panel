<?php

namespace Pterodactyl\Models;

use Illuminate\Support\Collection;

class Permission extends Model
{
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'subuser_permission';

    /**
     * Constants defining different permissions available.
     */
    const ACTION_WEBSOCKET = 'websocket.*';
    const ACTION_CONTROL_CONSOLE = 'control.console';
    const ACTION_CONTROL_START = 'control.start';
    const ACTION_CONTROL_STOP = 'control.stop';
    const ACTION_CONTROL_RESTART = 'control.restart';

    const ACTION_DATABASE_READ = 'database.read';
    const ACTION_DATABASE_CREATE = 'database.create';
    const ACTION_DATABASE_UPDATE = 'database.update';
    const ACTION_DATABASE_DELETE = 'database.delete';
    const ACTION_DATABASE_VIEW_PASSWORD = 'database.view_password';

    const ACTION_SCHEDULE_READ = 'schedule.read';
    const ACTION_SCHEDULE_CREATE = 'schedule.create';
    const ACTION_SCHEDULE_UPDATE = 'schedule.update';
    const ACTION_SCHEDULE_DELETE = 'schedule.delete';

    const ACTION_USER_READ = 'user.read';
    const ACTION_USER_CREATE = 'user.create';
    const ACTION_USER_UPDATE = 'user.update';
    const ACTION_USER_DELETE = 'user.delete';

    const ACTION_ALLOCATION_READ = 'allocation.read';
    const ACTION_ALLOCIATION_UPDATE = 'allocation.update';

    const ACTION_FILE_READ = 'file.read';
    const ACTION_FILE_CREATE = 'file.create';
    const ACTION_FILE_UPDATE = 'file.update';
    const ACTION_FILE_DELETE = 'file.delete';
    const ACTION_FILE_ARCHIVE = 'file.archive';
    const ACTION_FILE_SFTP = 'file.sftp';

    const ACTION_SETTINGS_RENAME = 'settings.rename';
    const ACTION_SETTINGS_REINSTALL = 'settings.reinstall';

    /**
     * Should timestamps be used on this model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Fields that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'subuser_id' => 'integer',
    ];

    /**
     * @var array
     */
    public static $validationRules = [
        'subuser_id' => 'required|numeric|min:1',
        'permission' => 'required|string',
    ];

    /**
     * All of the permissions available on the system. You should use self::permissions()
     * to retrieve them, and not directly access this array as it is subject to change.
     *
     * @var array
     * @see \Pterodactyl\Models\Permission::permissions()
     */
    protected static $permissions = [
        'websocket' => [
            'description' => 'Allows the user to connect to the server websocket, giving them access to view console output and realtime server stats.',
            'keys' => [
                '*' => 'Gives user full read access to the websocket.',
            ],
        ],

        'control' => [
            'description' => 'Permissions that control a user\'s ability to control the power state of a server, or send commands.',
            'keys' => [
                'console' => 'Allows a user to send commands to the server instance via the console.',
                'start' => 'Allows a user to start the server if it is stopped.',
                'stop' => 'Allows a user to stop a server if it is running.',
                'restart' => 'Allows a user to perform a server restart. This allows them to start the server if it is offline, but not put the server in a completely stopped state.',
            ],
        ],

        'user' => [
            'description' => 'Permissions that allow a user to manage other subusers on a server. They will never be able to edit their own account, or assign permissions they do not have themselves.',
            'keys' => [
                'create' => 'Allows a user to create new subusers for the server.',
                'read' => 'Allows the user to view subusers and their permissions for the server.',
                'update' => 'Allows a user to modify other subusers.',
                'delete' => 'Allows a user to delete a subuser from the server.',
            ],
        ],

        'file' => [
            'description' => 'Permissions that control a user\'s ability to modify the filesystem for this server.',
            'keys' => [
                'create' => 'Allows a user to create additional files and folders via the Panel or direct upload.',
                'read' => 'Allows a user to view the contents of a directory and read the contents of a file. Users with this permission can also download files.',
                'update' => 'Allows a user to update the contents of an existing file or directory.',
                'delete' => 'Allows a user to delete files or directories.',
                'archive' => 'Allows a user to archive the contents of a directory as well as decompress existing archives on the system.',
                'sftp' => 'Allows a user to connect to SFTP and manage server files using the other assigned file permissions.',
            ],
        ],

        // Controls permissions for editing or viewing a server's allocations.
        'allocation' => [
            'description' => 'Permissions that control a user\'s ability to modify the port allocations for this server.',
            'keys' => [
                'read' => 'Allows a user to view the allocations assigned to this server.',
                'update' => 'Allows a user to modify the allocations assigned to this server.',
            ],
        ],

        // Controls permissions for editing or viewing a server's startup parameters.
        'startup' => [
            'description' => 'Permissions that control a user\'s ability to view this server\'s startup parameters.',
            'keys' => [
                'read' => '',
                'update' => '',
            ],
        ],

        'database' => [
            'description' => 'Permissions that control a user\'s access to the database management for this server.',
            'keys' => [
                'create' => 'Allows a user to create a new database for this server.',
                'read' => 'Allows a user to view the database associated with this server.',
                'update' => 'Allows a user to rotate the password on a database instance. If the user does not have the view_password permission they will not see the updated password.',
                'delete' => 'Allows a user to remove a database instance from this server.',
                'view_password' => 'Allows a user to view the password associated with a database instance for this server.',
            ],
        ],

        'schedule' => [
            'description' => 'Permissions that control a user\'s access to the schedule management for this server.',
            'keys' => [
                'create' => '', // task.create-schedule
                'read' => '', // task.view-schedule, task.list-schedules
                'update' => '', // task.edit-schedule, task.queue-schedule, task.toggle-schedule
                'delete' => '', // task.delete-schedule
            ],
        ],

        'settings' => [
            'description' => 'Permissions that control a user\'s access to the settings for this server.',
            'keys' => [
                'rename' => '',
                'reinstall' => '',
            ],
        ],
    ];

    /**
     * Returns all of the permissions available on the system for a user to
     * have when controlling a server.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function permissions(): Collection
    {
        return Collection::make(self::$permissions);
    }

    /**
     * A list of all permissions available for a user.
     *
     * @var array
     * @deprecated
     */
    protected static $deprecatedPermissions = [
        'power' => [
            'power-start' => 's:power:start',
            'power-stop' => 's:power:stop',
            'power-restart' => 's:power:restart',
            'power-kill' => 's:power:kill',
            'send-command' => 's:command',
        ],
        'subuser' => [
            'list-subusers' => null,
            'view-subuser' => null,
            'edit-subuser' => null,
            'create-subuser' => null,
            'delete-subuser' => null,
        ],
        'server' => [
            'view-allocations' => null,
            'edit-allocation' => null,
            'view-startup' => null,
            'edit-startup' => null,
        ],
        'database' => [
            'view-databases' => null,
            'reset-db-password' => null,
            'delete-database' => null,
            'create-database' => null,
        ],
        'file' => [
            'access-sftp' => null,
            'list-files' => 's:files:get',
            'edit-files' => 's:files:read',
            'save-files' => 's:files:post',
            'move-files' => 's:files:move',
            'copy-files' => 's:files:copy',
            'compress-files' => 's:files:compress',
            'decompress-files' => 's:files:decompress',
            'create-files' => 's:files:create',
            'upload-files' => 's:files:upload',
            'delete-files' => 's:files:delete',
            'download-files' => 's:files:download',
        ],
        'task' => [
            'list-schedules' => null,
            'view-schedule' => null,
            'toggle-schedule' => null,
            'queue-schedule' => null,
            'edit-schedule' => null,
            'create-schedule' => null,
            'delete-schedule' => null,
        ],
    ];

    /**
     * Return a collection of permissions available.
     *
     * @param bool $array
     * @return array|\Illuminate\Database\Eloquent\Collection
     * @deprecated
     */
    public static function getPermissions($array = false)
    {
        if ($array) {
            return collect(self::$deprecatedPermissions)->mapWithKeys(function ($item) {
                return $item;
            })->all();
        }

        return collect(self::$deprecatedPermissions);
    }
}
