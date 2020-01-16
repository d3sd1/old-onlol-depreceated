<?php
require('core/core.php');
if(empty($_POST['game_key']) or empty($_POST['game_id']) or empty($_POST['server']))
{
	header('Location: '.URL.'/?invalid_data=game_spectate');
}

/* Parse server */
switch($_POST['server'])
{
	case 'euw':
	$server_ext = 'EUW1';
	$server_inf = 'spectator.euw1.lol.riotgames.com:80';
	break;
	case 'na':
	$server_ext = 'NA1';
	$server_inf = 'spectator.na.lol.riotgames.com:80';
	break;
	case 'br':
	$server_ext = 'BR1';
	$server_inf = '';
	break;
	case 'kr':
	$server_ext = 'KR';
	$server_inf = 'spectator.kr.lol.riotgames.com:80';
	break;
	case 'tr':
	$server_ext = 'TR1';
	$server_inf = 'spectator.tr.lol.riotgames.com:80';
	break;
	case 'eune':
	$server_ext = 'EUN1';
	$server_inf = 'spectator.eu.lol.riotgames.com:8088';
	break;
	case 'lan':
	$server_ext = 'LA1';
	$server_inf = 'spectator.la1.lol.riotgames.com:80';
	break;
	case 'las':
	$server_ext = 'LA2';
	$server_inf = 'spectator.la2.lol.riotgames.com:80';
	break;
	case 'ru':
	$server_ext = 'RU';
	$server_inf = 'spectator.ru.lol.riotgames.com:80';
	break;
	case 'oce':
	$server_ext = 'OC1';
	$server_inf = 'spectator.oc1.lol.riotgames.com:80';
	break;
	case 'pbe':
	$server_ext = 'PBE1';
	$server_inf = 'spectator.pbe1.lol.riotgames.com:8088';
	break;
	default: 
	$server_ext = 'EUW1';
	$server_inf = 'spectator.euw1.lol.riotgames.com:80';
}
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"game_".$_POST['game_id'].".bat\"");
echo '@REM ÀÌ ÆÄÀÏÀ» ÀÌ¿ëÇÏ½Ç °æ¿ì ²À ¾Æ·¡ÀÇ ÃâÃ³°¡ Ç¥½ÃµÇµµ·Ï ÇØÁÖ¼¼¿ä.
@echo off
setlocal enabledelayedexpansion
set RADS_PATH=
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKCU\SOFTWARE\RIOT GAMES\RADS" /v "LOCALROOTFOLDER"`) DO  (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKCU\SOFTWARE\Classes\VirtualStore\MACHINE\SOFTWARE\Wow6432Node\RIOT GAMES\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKCU\SOFTWARE\Classes\VirtualStore\MACHINE\SOFTWARE\RIOT GAMES\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKLM\Software\Wow6432Node\Riot Games\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKLM\Software\Wow6432Node\Riot Games\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKCU\SOFTWARE\RIOT GAMES\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
FOR /f "usebackq skip=2 tokens=3,4,5,6,7,8,9" %%i in (`%systemroot%\system32\REG.EXE QUERY "HKLM\SOFTWARE\RIOT GAMES\RADS" /v "LOCALROOTFOLDER"`) DO (
	SET RADS_PATH=%%i %%j %%k %%l %%m %%n %%o
	goto runApp
)
cls
for /f "Tokens=3,4,5,6,7,8,9,10,11,12,13,14,15" %%a in (\'%systemroot%\system32\REG.EXE Query HKLM\Software /V /F "LocalRootFolder" /S /E ^| %systemroot%\system32\find.exe "RADS"\') do (
	set RADS_PATH=%%a %%b %%c %%d %%e %%f %%g %%h %%i %%j %%k %%l %%m
	goto runApp
)
cls
for /f "Tokens=3,4,5,6,7,8,9,10,11,12,13,14,15" %%a in (\'%systemroot%\system32\REG.EXE Query HKLM\Software /s ^| %systemroot%\system32\find.exe "LocalRootFolder" ^| %systemroot%\system32\find.exe "RADS"\') do (
	set RADS_PATH=%%a %%b %%c %%d %%e %%f %%g %%h %%i %%j %%k %%l %%m
	goto runApp
)
cls
for /f "Tokens=3,4,5,6,7,8,9,10,11,12,13,14,15" %%a in (\'%systemroot%\system32\REG.EXE Query HKCU\Software /V /F "LocalRootFolder" /S /E ^| %systemroot%\system32\find.exe "RADS"\') do (
	set RADS_PATH=%%a %%b %%c %%d %%e %%f %%g %%h %%i %%j %%k %%l %%m
	goto runApp
)
cls
for /f "Tokens=3,4,5,6,7,8,9,10,11,12,13,14,15" %%a in (\'%systemroot%\system32\REG.EXE Query HKCU\Software /s ^| %systemroot%\system32\find.exe "LocalRootFolder" ^| %systemroot%\system32\find.exe "RADS"\') do (
	set RADS_PATH=%%a %%b %%c %%d %%e %%f %%g %%h %%i %%j %%k %%l %%m
	goto runApp
)
cls
goto cannotFind
:runApp
set RADS_PATH=%RADS_PATH:/=\%
@cd /d "%RADS_PATH%\solutions\lol_game_client_sln\releases"

set init=0
set v0=0&set v1=0&set v2=0&set v3=0
for /f "delims=" %%F in (\'dir *.*.*.* /b\') do (
	for /F "tokens=1,2,3,4 delims=." %%i in ("%%F") do (
		if !init! equ 0 ( set init=1&set flag=1 ) else (
			set flag=0
			
			if %%i gtr !v0! ( set flag=1 ) else (
				if %%j gtr !v1! ( set flag=1 ) else (
					if %%k gtr !v2! ( set flag=1 ) else (
						if %%l gtr !v3! ( set flag=1 )
					)
				)
			)
		)
		
		if !flag! gtr 0 (
			set v0=%%i&set v1=%%j&set v2=%%k&set v3=%%l
		)
	)
)

if !init! equ 0 goto cannotFind
set lolver=!v0!.!v1!.!v2!.!v3!
/* if exist "LolClient.exe" (
TASKKILL /IM "LolClient.exe" /F

) */

@cd /d "!RADS_PATH!\solutions\lol_game_client_sln\releases\!lolver!\deploy"
if exist "League of Legends.exe" (
	@start "" "League of Legends.exe" "8394" "LoLLauncher.exe" "" "spectator '.$server_inf.' '.$_POST['game_key'].' '.$_POST['game_id'].' '.$server_ext.'"
	goto exit
)
:cannotFind
echo EN: Cannot found LOL directory path for automatic. Error #3AxDgtt4
@pause
goto exit
:exit';
?>