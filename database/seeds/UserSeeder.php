<?php

namespace Sardoj\Uccello\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Sardoj\Uccello\User;
use Sardoj\Uccello\Module;
use Sardoj\Uccello\Tab;
use Sardoj\Uccello\Block;
use Sardoj\Uccello\Field;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createData();
        $module = $this->createModule();
        $this->createTabsBlocksFields($module);
        
    }

    public function createData()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@uccello.io',
            'password' => Hash::make( '123456')
        ]);
    }

    protected function createModule()
    {
        return Module::create([
            'name' => 'user',
            'icon' => 'person',
            'entity_class' => 'User',
            'is_for_admin' => true
        ]);
    }

    protected function createTabsBlocksFields($module)
    {
        // Main tab
        $tab = Tab::create([
            'label' => 'tab.main',
            'icon' => null,
            'sequence' => 0,
            'module_id' => $module->id
        ]);

        // Auth block
        $block = Block::create([
            'label' => 'block.auth',
            'icon' => 'lock',
            'description' => 'block.auth.description',
            'sequence' => 0,
            'tab_id' => $tab->id
        ]);

        // Username
        Field::create([
            'name' => 'username',
            'label' => 'field.username',
            'uitype' => Field::UITYPE_TEXT,
            'display_type' => Field::DISPLAY_TYPE_EVERYWHERE,
            'data' => ['rules' => 'required|alpha_dash|unique:users,username'],
            'sequence' => 0,
            'block_id' => $block->id
        ]);

        // Is Admin
        Field::create([
            'name' => 'is_admin',
            'label' => 'field.is_admin',
            'uitype' => Field::UITYPE_CHECKBOX,
            'display_type' => Field::DISPLAY_TYPE_EVERYWHERE,
            'sequence' => 1,
            'block_id' => $block->id
        ]);

        // Password
        Field::create([
            'name' => 'password',
            'label' => 'field.password',
            'uitype' => Field::UITYPE_PASSWORD,
            'display_type' => Field::DISPLAY_TYPE_CREATE_ONLY,
            'data' => ['rules' => 'min:6', 'repeated' => true],
            'sequence' => 2,
            'block_id' => $block->id
        ]);

        // Contact block
        $block = Block::create([
            'label' => 'block.contact',
            'icon' => 'person',
            'sequence' => 1,
            'tab_id' => $tab->id
        ]);

        // Email
        Field::create([
            'name' => 'email',
            'label' => 'field.email',
            'uitype' => Field::UITYPE_EMAIL,
            'display_type' => Field::DISPLAY_TYPE_EVERYWHERE,
            'data' => ['rules' => 'required|email|unique:users,email'],
            'sequence' => 0,
            'block_id' => $block->id
        ]);

        // Phone
        Field::create([
            'name' => 'phone',
            'label' => 'field.phone',
            'uitype' => Field::UITYPE_TEL,
            'display_type' => Field::DISPLAY_TYPE_EVERYWHERE,
            'sequence' => 1,
            'block_id' => $block->id
        ]);
    }
}
