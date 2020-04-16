<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnippetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snippets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fork_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('body');
            $table->string('settings', 1000)->default('{"indentUnit": 2, "smartIndent": true, "tabSize": 2, "indentWithTabs": false, "electricChars": true, "direction": "ltr", "mode": "default", "theme": "default", "lineWrapping": true, "lineNumbers": true, "readOnly": false, "undoDepth": 200, "autofocus": true, "cursorBlinkRate": 530, "cursorScrollMargin": 0, "cursorHeight": 1, "workTime": 200, "workDelay": 300, "spellcheck": false, "autocorrect": false, "autocapitalize": false, "line": true}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('snippets');
    }
}
