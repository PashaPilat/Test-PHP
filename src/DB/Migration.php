<?php
namespace App\DB;

abstract class Migration {
    abstract public function up();
    abstract public function down();
}
