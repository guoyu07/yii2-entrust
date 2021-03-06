<?php
namespace vmorozov\entrust\traits;

use vmorozov\entrust\models\Role;
use vmorozov\entrust\models\Permission;

/**
 * Trait to make getting access to role system in User model easier.
 *
 * Class EntrustUserTrait
 * @package vmorozov\entrust\traits
 */
trait EntrustUserTrait
{
    /**
     * Many-to-Many relations with Role.
     *
     */
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['id' => 'role_id'])
            ->viaTable('role_user', ['user_id' => 'id']);
    }

    /**
     * Get Role of user.
     *
     * @return mixed
     */
    public function role()
    {
        return $this->getRoles()->one();
    }

    /**
     * Checks if user has role with given name.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName)
    {
        return $this->role()->name == $roleName;
    }
    /**
     * Many-to-Many relations with Permission.
     *
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['id' => 'permission_id'])
            ->viaTable('permission_user', ['user_id' => 'id']);
    }

    /**
     * Checks if this user has permission with given name.
     *
     * @param string $permissionName - name of permission.
     *
     * @return bool
     */
    public function hasPermission(string $permissionName)
    {
        if ($this->getPermissions()->where(['name' => $permissionName])->count() >= 1)
            return true;
        return false;
    }

    /**
     * Assign given role to this user. Adds permissions of new role to user.
     * If this user already has role, unlinks it before assigning a new one.
     *
     * @param Role $role
     */
    public function assignRole(Role $role)
    {
        if ($this->role() != null)
        {
            $this->detachRolePermissions();
            $this->unlink('roles', $this->role(), true);
        }

        $this->link('roles', $role);
        $this->attachRolePermissions();
    }

    /**
     * Attach all permissions of current role to user.
     */
    private function attachRolePermissions()
    {
        foreach ($this->role()->permissions as $permission)
        {
            $this->attachPermission($permission);
        }
    }

    /**
     * Remove permissions of current role from user.
     */
    private function detachRolePermissions()
    {
        foreach ($this->role()->permissions as $permission)
        {
            $this->detachPermission($permission);
        }
    }

    /**
     * Add given permission to user permissions.
     * You can give to this method permission name string or Permission object.
     *
     * @param string|Permission $permission
     */
    public function attachPermission($permission)
    {
        if (is_string($permission))
            $permission = Permission::findOne(['name' => $permission]);

        if ($this->hasPermission($permission->name))
            return;

        $this->link('permissions', $permission);
    }

    /**
     * Remove given permissions from user.
     * You can give to this method permission name string or Permission object.
     *
     * @param string|Permission $permission
     */
    public function detachPermission($permission)
    {
        if (is_string($permission))
            $permission = Permission::findOne(['name' => $permission]);

        $this->unlink('permissions', $permission, true);
    }
}
