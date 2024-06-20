@echo off
@cd phar
@php build_phar.php
@ls -las ./*.phar
@cd ..