<?php
namespace vmorozov\entrust\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `posts`.
 */
class m170221_153727_create_entrust_tables extends Migration
{
    /**
     * Table of User model to use it in relations.
     * @var
     */
    private $usersTable;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::construct;
        $this->usersTable = app\models\User::tableName();
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->db = app\models\User::getDb();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('roles', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'display_name' => $this->string(),
            'description' => $this->string(),
        ]);

        $this->createTable('permissions', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'display_name' => $this->string(),
            'description' => $this->string(),
        ]);

        $this->createTable('permission_role', [
            'role_id' => $this->integer(),
            'permission_id' => $this->integer()
        ]);
        // add foreign key for table `permission_role`
        $this->addForeignKey(
            'fk-permission_role-role_id',
            'permission_role',
            'role_id',
            'roles',
            'id',
            'CASCADE'
        );
        // add foreign key for table `permission_role`
        $this->addForeignKey(
            'fk-permission_role-permission_id',
            'permission_role',
            'permission_id',
            'permissions',
            'id',
            'CASCADE'
        );

        $this->createTable('role_user', [
            'role_id' => $this->integer(),
            'user_id' => $this->integer(),
        ]);
        // add foreign key for table `role_user`
        $this->addForeignKey(
            'fk-role_user-role_id',
            'role_user',
            'role_id',
            'roles',
            'id',
            'CASCADE'
        );
        // add foreign key for table `role_user`
        $this->addForeignKey(
            'fk-role_user-role_id',
            'role_user',
            'user_id',
            $this->usersTable,
            'id',
            'CASCADE'
        );

        $this->createTable('permission_user', [
            'permission_id' => $this->integer(),
            'user_id' => $this->integer(),
        ]);
        // add foreign key for table `permission_user`
        $this->addForeignKey(
            'fk-role_user-permission_id',
            'permission_user',
            'permission_id',
            'permissions',
            'id',
            'CASCADE'
        );
        // add foreign key for table `permission_user`
        $this->addForeignKey(
            'fk-role_user-role_id',
            'permission_user',
            'user_id',
            $this->usersTable,
            'id',
            'CASCADE'
        );


    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-permission_role-role_id',
            'permission_role'
        );
        $this->dropForeignKey(
            'fk-permission_role-permission_id',
            'permission_role'
        );
        $this->dropForeignKey(
            'fk-role_user-role_id',
            'role_user'
        );
        $this->dropForeignKey(
            'fk-role_user-role_id',
            'role_user'
        );
        $this->dropForeignKey(
            'fk-role_user-permission_id',
            'permission_user'
        );
        $this->dropForeignKey(
            'fk-role_user-role_id',
            'permission_user'
        );

        $this->dropTable('roles');
        $this->dropTable('permissions');
        $this->dropTable('permission_role');
        $this->dropTable('role_user');
        $this->dropTable('permission_user');
    }
}