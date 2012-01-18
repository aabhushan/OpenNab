<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright c 2007 OpenNab team - http://opennab.sourceforge.net/team/

This file is part of OpenNab

OpenNab is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

OpenNab is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public
License along with this script; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************/

class File
{
	var $filepath;
	
	function File($filepath)
	{
		$this->filepath = $filepath;
	}

	function Exists()
	{
		return file_exists($this->filepath);
	}

	function Read()
	{
		return file_get_contents($this->filepath);
	}
	
	function MakeFolder($foldername) {
		if( strpos(OPENNAB_BASE_DIR, $foldername) !== false )
			return;
		if( file_exists($foldername) )
			return;
		File::MakeFolder(dirname($foldername));
		mkdir($foldername);
	}
	
	function ScanFolder($foldername) {
    $contents = array();
    if( !is_dir($foldername) )
      return false;
    $folderHandle = @opendir($foldername);
    if( !$folderHandle )
      return false;
    while( false !== ($filename = @readdir($folderHandle)) ) {
      if( substr($filename,0,1) == '.' )
        continue;
      $fullfilename = $foldername.'/'.$filename;
      if( $subdirs = File::ScanFolder($fullfilename) )
        $contents = array_merge( $contents, $subdirs );
      else
        $contents[] = $fullfilename;
    }
    return $contents;
	}
	
	function DelFolder($foldername) {
    $folderHandle = @opendir($foldername);
    if( !$folderHandle ) {
      @unlink($foldername);
      return;
    }
    while( false !== ($filename = @readdir($folderHandle)) ) {
      if( substr($filename,0,1) == '.' )
        continue;
      $fullfilename = $foldername.'/'.$filename;
      File::DelFolder($fullfilename);
    }
    @rmdir($foldername);
	}
	
	function Write(&$data) {
		$this->MakeFolder(dirname($this->filepath));
		fileWrite($this->filepath,$data);
	}

}

?>
