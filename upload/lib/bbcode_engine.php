<?php
/*
	File: 		lib/bbcode_engine.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The base BBCode Engine for Chivalry Engine.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('JBBCode/Parser.php');
require_once("JBBCode/visitors/SmileyVisitor.php");
$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

//URL with option
$builder = new JBBCode\CodeDefinitionBuilder('url', '<a href="{option}">{param}</a>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//URL
$builder = new JBBCode\CodeDefinitionBuilder('url', '<a href="{param}">{param}</a>');
$parser->addCodeDefinition($builder->build());

//Quote with option 
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<blockquote class="blockquote">
                                                            <p class="mb-0">{param}</p>
                                                            <footer class="blockquote-footer">
                                                            <cite>{option}</cite></footer></blockquote>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//Quote w/o option
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<blockquote class="blockquote"><p class="mb-0">{param}</p></blockquote>');
$parser->addCodeDefinition($builder->build());

//Keyboard
$builder = new JBBCode\CodeDefinitionBuilder('kbd', '<kbd>{param}</kbd>');
$parser->addCodeDefinition($builder->build());

//Code
$builder = new JBBCode\CodeDefinitionBuilder('code', '<code>{param}</code>');
$parser->addCodeDefinition($builder->build());

//Rage
$builder = new JBBCode\CodeDefinitionBuilder('rage', '<div class="text-uppercase"><b><big>{param}</b></big></div>');
$parser->addCodeDefinition($builder->build());

//Abbreviate
$builder = new JBBCode\CodeDefinitionBuilder('abbr', '<abbr data-toggle="tooltip" title="{option}">{param}</abbr>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//Highlight
$builder = new JBBCode\CodeDefinitionBuilder('mark', '<mark>{param}</mark>');
$parser->addCodeDefinition($builder->build());

//Strikethrough
$builder = new JBBCode\CodeDefinitionBuilder('s', '<s>{param}</s>');
$parser->addCodeDefinition($builder->build());

//Right align
$builder = new JBBCode\CodeDefinitionBuilder('right', '<div align="right">{param}</div>');
$parser->addCodeDefinition($builder->build());

//Left align
$builder = new JBBCode\CodeDefinitionBuilder('left', '<div align="left">{param}</div>');
$parser->addCodeDefinition($builder->build());

//Size
$builder = new JBBCode\CodeDefinitionBuilder('size', '<font size="{option}">{param}</font>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//YouTube Video
$builder = new JBBCode\CodeDefinitionBuilder('youtube', '<div class="embed-responsive embed-responsive-16by9">
                                                            <iframe class="embed-responsive-item" id="ytplayer"
                                                                type="text/html"
                                                                src="https://www.youtube.com/embed/{param}/ frameborder="0">
                                                            </iframe></div>');
$parser->addCodeDefinition($builder->build());