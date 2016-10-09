<?php

use Phpmig\Migration\Migration;

class Roster extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $container = $this->getContainer();
        $table = new Doctrine\DBAL\Schema\Table('roster');
        $table->addColumn('id', 'integer', array('autoincrement' => true));
        $table->addColumn('userId', 'integer', array('null' => false));
        $table->addColumn('QQ', 'string', array('default' => null, 'length' => 32, 'comment' => 'QQ'));
        $table->addColumn('constellation', 'string', array('default' => null, 'length' => 255, 'comment' => '星座'));
        $table->addColumn('hobby', 'string', array('default' => null, 'length' => 255, 'comment' => '爱好'));
        $table->addColumn('favoriteMusic', 'string', array('default' => null, 'length' => 255, 'comment' => '喜欢的音乐'));
        $table->addColumn('favoriteMovie', 'string', array('default' => null, 'length' => 255, 'comment' => '喜欢的电影'));
        $table->addColumn('favoriteSport', 'string', array('default' => null, 'length' => 255, 'comment' => '喜欢的运动'));
        $table->addColumn('favoriteFood', 'string', array('default' => null, 'length' => 255, 'comment' => '喜欢的食物'));
        $table->addColumn('favoritePlace', 'string', array('default' => null, 'length' => 255, 'comment' => '喜欢的地方'));
        $table->addColumn('dream', 'string', array('default' => null, 'length' => 255, 'comment' => '梦想'));
        $table->setPrimaryKey(array('id'));
        $container['db']->getSchemaManager()->createTable($table);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $container = $this->getContainer();
        $container['db']->getSchemaManager()->dropTable('roster');
    }
}
