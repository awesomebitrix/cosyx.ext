<?php
	// NOTE: We set max execution time for this script to 10 minutes becuase writing the Excel data can take a long time
	// NOTE: If you are outputing a LOT of Excel data and need larger amounts of memory, set the memory_limit higher
	include_once 'Classes/PHPExcel.php';
	include_once 'Classes/PHPExcel/IOFactory.php';
	include_once 'Classes/PHPExcel/Cell/Hyperlink.php';
	include_once 'Classes/PHPExcel/Cell.php';
	include_once 'Classes/PHPExcel/Style/Color.php';
	include_once 'Classes/PHPExcel/Style/Fill.php';
	//ini_set('memory_limit', 512);
	ini_set('max_execution_time', 600); // Set to 10 minutes to give enough time to write the data

	class PHPExcelWrapperType {
		const Excel5 = 0;
		const Excel2007 = 1;
		const CSV = 2;
	}

	class PHPExcelTextDirection {
		const Clockwise = 0;
		const CounterClockwise = 1;
		const Stacked = 2;
	}

	// These are taken directly from PHPExcel/Style/Border.php
	// They are duplicated becuase the point of this wrapper is to not have
	// to include a whole bunch of other files in whatver script your using
	class PHPExcelBorderStyle {
		const BORDER_NONE				= 'none';
		const BORDER_DASHDOT			= 'dashDot';
		const BORDER_DASHDOTDOT			= 'dashDotDot';
		const BORDER_DASHED				= 'dashed';
		const BORDER_DOTTED				= 'dotted';
		const BORDER_DOUBLE				= 'double';
		const BORDER_HAIR				= 'hair';
		const BORDER_MEDIUM				= 'medium';
		const BORDER_MEDIUMDASHDOT		= 'mediumDashDot';
		const BORDER_MEDIUMDASHDOTDOT	= 'mediumDashDotDot';
		const BORDER_MEDIUMDASHED		= 'mediumDashed';
		const BORDER_SLANTDASHDOT		= 'slantDashDot';
		const BORDER_THICK				= 'thick';
		const BORDER_THIN				= 'thin';
	}
	
	
	class PHPExcelBorderType {
		const Left = 'left';
		const Right = 'right';
		const Top = 'top';
		const Bottom = 'bottom';
		const All = 'allborders';
		const None = 'none';
	}
	
	/**
	*
	* PHPExcelWrapper class is a wrapper for the PHPExcel library
	*
	* The PHPExcelWrapper class aims to make reading/writing Excel files easier by
	* creating an overall stream object. The PHPExcelWrapper class can be either
	* a CSV or an Excel5/2007 type. There are no 'reading' or 'get' methods within
	* this class becuase of the idea that it is much easier to read in data from
	* a CSV than an Excel file. Thus we implement the AutoConvert static function
	* which you can use to convert (if needed) an Excel file to a CSV file and
	* read in the data via a file object and explode(',', $line)
	* NOTE: if the Type is CSV, then a lot of functions in this class do not
	*       do anything (e.g. AutoFit() simply returns out)
	*/
	class PHPExcelWrapper {
		
		/**********************************PROPERTIES**********************************/
		
		/**
		* The underlying PHPExcel object
		*/
		private $PhpXl;
		
		/**
		* The underlying PHPExcelWriter interface
		*/
		private $PhpXlWriter;
		
		/**
		* The underlying PHPExcelReader interface
		*/
		private $PhpXlReader; 
		
		/**
		* The underlying file handle (for CSV only)
		*/
		private $Handle;
		
		/**
		* (string) The current file name
		*/
		public $FileName;
		
		/**
		* (bool) Value indicating if the current stream is open
		*/
		public $IsOpen;
		
		/**
		* (int) The current row of the Excel file the underlying stream is on
		*/
		public $CurrentRow;
		
		/**
		* (string) The type this object is (CSV/Excel5/Excel2007)
		*/
		public $Type;
		
		/********************************END PROPERTIES********************************/
		
		/******************************STATIC FUNCTIONS********************************/
		
		/**
		* Automatically convert a file type to another
		*
		* Automatically convert a file type to another. (From CSV to XLS/XLSX and back)
		* The purpose of this function is primarly to convert an XLS/XLSX file to a CSV
		* file for ease of reading data (just open a file handle, read line by line
		* and do an explode(',', $line)
		*
		* @param 	string					$fileToConvert	The file name to convert. It doesn't have to have an extnesion as 
		*													PHPExcel can auto open in the proper format
		* @param	string					$newFileName	(OPTIONAL) The name of the new file to save.
		*                                                   This is file name ONLY (no folder path or extension)
		*                                                   Default is tmp.
		* @param 	PHPExcelWrapperType		$typeTo			(OPTIONAL) The type to convert to, either Excel5, Excel2007 or CSV. 
		*													Default is Excel5.
		* @param 	bool					$deleteOldFile	(OPTIONAL) True to automatically delete the $fileToConvert file.
		*													Default is false.
		* @return	string									The new file name of the converted file
		*/
		public static function AutoConvert($fileToConvert, $newFileName = 'tmp', $typeTo = PHPExcelWrapperType::Excel5, $deleteOldFile = false) {
			// NOTE: Any saving/reading to the Excel2007 format needs php_zip.so to operate
			$WriterType = ''; $NewFileName = ''; $Ext = '';
			switch ($typeTo) {
				case PHPExcelWrapperType::Excel5:
					$WriterType = 'Excel5'; $Ext = 'xls';
					break;
				case PHPExcelWrapperType::Excel2007:
					$WriterType = 'Excel2007'; $Ext = 'xlsx';
					break;
				case PHPExcelWrapperType::CSV:
					$WriterType = 'CSV'; $Ext = 'csv';
					break;
			}
			$NewFileName = PHPExcelWrapper::GetNewFileName('/tmp', $newFileName, $Ext);
			$Auto = PHPExcel_IOFactory::load($fileToConvert);
			$Writer = PHPExcel_IOFactory::createWriter($Auto, $WriterType);
			$Writer->save($NewFileName);
			if ($deleteOldFile) {
				unlink($fileToConvert);
			}
			return $NewFileName;
		}
		
		/**
		 * Gets a new file name for a relevently named temp file
		 *
		 * This function will get a new file name based on the parameters passed in
		 * If a file exists in the directory it will increment a counter and append
		 * it between the file name and extension.
		 * 
		 * @param      string	$dir   			The directory to look at for a new file name
		 * @param      string	$oldFileName	The old file name
		 * @param      string	$ext 			The extension of the file
		 *
		 * @returns	   string value of the new file name
		 */
		public static function GetNewFileName($dir, $oldFileName, $ext) {
			$Idx = 0;
			if (substr($dir, (strlen($dir) - 1), 1) != "/") { $dir .= '/'; }
			if (substr($ext, 0, 1) != ".") { $ext = ".".$ext; }
			if (!file_exists(($dir.$oldFileName.$ext))) { return ($dir.$oldFileName.$ext); }
			do {
				$FullName = $dir.$oldFileName.'.'.($Idx++).$ext;
			} while(file_exists($FullName));
			$Idx--;
			return $dir.$oldFileName.'.'.$Idx.$ext;
		}
		
		/****************************END STATIC FUNCTIONS******************************/
		
		/********************************MAIN FUNCTIONS********************************/
		
		/*
		* The overloaded constructor for the PHPExcelWrapper class
		*
		* @param 	string	$fileName	The file name to use (can be relative or absolute)
		* @param 	int		$type		(OPTIONAL) The type of wrapper to load (0 for Excel5 (default), 1 for Excel2007, 2 for CSV)
		*/
		function PHPExcelWrapper($fileName, $type = PHPExcelWrapperType::Excel5) {
			$this->IsOpen = false;
			$this->Open($fileName, $type);
		}
		
		/**
		 * Flushes out and saves any data and closes all underlying streams
		 */
		public function Close() {
			$this->Flush();
			if ($this->Type == PHPExcelWrapperType::CSV) {
				fclose($this->Handle);
			} else {
				$this->PhpXl->disconnectWorksheets();
				$this->PhpXl->garbageCollect();
				$this->CurrentRow = 1;
			}
			$this->IsOpen = false;
			unset($this->PhpXl);
			unset($this->PhpXlWriter);
			unset($this->Handle);
		}
		
		/**
		 * Flushes any data to the file (saves the file)
		 */
		public function Flush() { 
			// CSV type doesn't need flush since it was open withw w+
			if ($this->Type != PHPExcelWrapperType::CSV) {
//				$this->PhpXlWriter->setPHPExcel($this->PhpXl);
				$this->PhpXlWriter->save($this->FileName);
			}
		}
		
		/**
		 * Get the underlying stream object
		 *
		 * @returns 	Either the file object if Type is CSV or the underlying PHPExcel object
		 */
		public function GetBaseStream() {
			if ($this->Type == PHPExcelWrapperType::CSV) { return $this->Handle; }
			return $this->PhpXl;
		}

		public function x() {
			if ($this->Type == PHPExcelWrapperType::CSV) { return $this->Handle; }
			return $this->PhpXl;
		}
		/**
		 * Gets the column name from a number (e.g. 2='B', 27='AA', etc.)
		 * 
		 * @param		int 	$col   The column number to convert
		 *
		 * @returns		A string representation of the column number
		 */
		public function GetExcelAlphaColumn($col) {
			$Div = $col; $Mod = 0; $Name = '';
			while ($Div > 0) {
				$Mod = ($Div - 1) % 26;
				$Name = 
				$Name = chr(65 + $Mod).$Name;
				$Div = (int)(($Div - $Mod) / 26);
			}
			return $Name;
		}
	
		/**
		 * Sets the column width 
		 * 
		 * @param		mixed 	$col   The column number to convert
		 * @param		int 	$width   Width in Excel ems
		 *
		 */
		public function SetColumnWidth($col, $width) {
			if (intval($col)) {
				$col = $this->GetExcelAlphaColumn($col);
			}
			$this->PhpXl->getActiveSheet()->getColumnDimension($col)->setWidth($width);
		}
	
		/**
		 * Gets the column number from column name (e.g. 'B'=2, 'AA'=27, etc.)
		 * 
		 * @param		string	$col   The column name to convert
		 *
		 * @returns		An integer value representation of the column name
		 */
		public function GetExcelColumnFromAlpha($col) {
			if (is_numeric($col)) { return $col; }
			$col = strtoupper($col);
			$Len = strlen($col); $Tot = 0;
			for ($i = 0; $i < $Len; $i++) {
				$Num = ord(substr($col, $i, 1)) - 64;
				$Pow = pow(26, $i) - 1;
				$Tot += ($Num + $Pow);
			}
			return $Tot;
		}
		
		/**
		 * Gets the Excel column name from a numeric column and row (e.g. 2 and 1 = 'B1', 27 and 2 = 'AA2', etc.)
		 * 
		 * @param		int		$col   The column to convert
		 * @param		int		$row   The row
		 *
		 * @returns		A string represenation of the column name and row number
		 */
		public function GetExcelAlphanumericColumnRow($col, $row) {
			$Name = $this->GetExcelAlphaColumn($col);
			return $Name.$row;
		}
		
		/**
		 * Gets a string representation of a cell coordinate
		 * 
		 * @param      mixed	$col   The column name/number (can be either an int or string)
		 * @param      int		$row   The row
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                      $col to be a numeric value. If this value is true
		 * 										this funcition simply concatenates $col and $row
		 *										Default is false.
		 *
		 * @returns		A string representation of a cell coordinate
		 */
		public function GetCellCoord($col, $row, $isCoordinate = false) {
			$CellCoord = 'A1';
			if ($isCoordinate) {
				$CellCoord = $col.$row;
			} else {
				if (is_numeric($col)) {
					$CellCoord = $this->GetExcelAlphanumericColumnRow($col, $row);
				} else {
					throw new Exception('Column ($col) must be a numeric value if $isCoordinate is false.');
				}
			}
			return $CellCoord;
		}
		
		/**
		 * Open a file
		 * 
		 * @param      string				$fileName   The file to open
		 * @param      PHPExcelWrapperType	$type   	(OPTIONAL) The type of file to open (CSV/Excel5/Excel2007). 
		 *                                              Default is PHPExcelWrapperType::Excel5.
		 */
		public function Open($fileName, $type = PHPExcelWrapperType::Excel5) {
			$this->FileName = $fileName;
			$this->Type = $type;
			if ($this->IsOpen) { $this->Close(); }
			$this->CurrentRow = 1; // Current row gets set to 1 (Excel is not 0 based)
			if ($this->Type == PHPExcelWrapperType::CSV) {
				$this->Handle = fopen($this->FileName, 'w+'); // Write/Read
			} else {
				$this->PhpXl = new PHPExcel();
				$this->PhpXl->setActiveSheetIndex(0);
				if ($this->Type == PHPExcelWrapperType::Excel2007) {
					$this->PhpXlWriter = new PHPExcel_Writer_Excel2007($this->PhpXl);
					$this->PhpXlReader = new PHPExcel_Reader_Excel2007($this->PhpXl);
				} else {
					$this->PhpXlWriter = new PHPExcel_Writer_Excel5($this->PhpXl);
					$this->PhpXlReader = new PHPExcel_Reader_Excel5($this->PhpXl);
				}
				$this->Flush();
			}
			$this->IsOpen = true;
		}
		
		/**
		 * Sets the active worksheet
		 * 
		 * @param      int		$index   The worksheet number to set
		 */
		public function SetActiveWorksheet($index) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$this->PhpXl->setActiveSheetIndex($index);
		}
		
		/**
		 * Save the current data and writes it to disk
		 */
		public function Save() {
			$this->Flush();
		}
		
		/*******************************END MAIN FUNCTIONS******************************/
		
		/********************************SET FUNCTIONS********************************/
		
		/**
		 * Set the columns in the Excel file to autofit the content
		 * 
		 * @param      int		$column	(OPTIONAL) The column to autofit. 
		 *								Default is 0. (0 says all columns with content).
		 */
		public function AutoFit($column = 0) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			if ($column > 0) { // Set a specific column
				$ColName = $this->GetExcelAlphaColumn($column);
				$this->PhpXl->getActiveSheet()->getColumnDimension($ColName)->setAutoSize(true);
			} else { // Set ALL columns
				$LastColumn = $this->PhpXl->getActiveSheet()->getHighestColumn(); // B
				$LastColumnIndex = PHPExcel_Cell::columnIndexFromString($LastColumn); // 2
				for ($i = 1; $i <= $LastColumnIndex; $i++) {
					$ColName = $this->GetExcelAlphaColumn($i);
					$this->PhpXl->getActiveSheet()->getColumnDimension($ColName)->setAutoSize(true);
				}
			}
		}

		public function SetWrap($col = 0, $row = 0, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$WholeRow = ($col == 0);
			$WholeCol = ($row == 0);
			// getHighsetColumn returns letter (AZ), need to convert to num
			if($col == 0) { $col = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn()); }
			if ($row == 0) { $row = $this->PhpXl->getActiveSheet()->getHighestRow(); }
			$CellCoordEnd = $this->GetCellCoord($col, $row, $isCoordinate);
			$CellCoordStart = 'A1';
			if (!$WholeCol || !$WholeRow) { // Only fall in here if one of them is false (Which means don't do all cells)
				if ($WholeCol) { $CellCoordStart = $this->GetCellCoord($col, 1, $isCoordinate); }
				if ($WholeRow) { $CellCoordStart = 'A'.$row; }
			}
			$CellRange = $CellCoordStart.':'.$CellCoordEnd;
			if ($CellCoordStart == $CellCoordEnd || (!$WholeCol && !$WholeRow)) { $CellRange = $CellCoordEnd; }
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getAlignment()->setWrapText(true);
		}


		/**
		 * Set the borders around cells in an Excel file
		 * 
		 * @param      int					$col   			(OPTIONAL) The column to set the borders around.
		 * 													Default is 0. (0 says all columns)
		 * @param      int					$row   			(OPTIONAL) The row to set the borders around.
		 * 													Default is 0. (0 says all rows)
		 * @param      PHPExcelBorderType	$borderSides   	(OPTIONAL) The sides to set the border on.
		 * 													Default is PHPExcelBorderType::All.
		 * @param      PHPExcelBorderStyle	$borderType   	(OPTIONAL) The border style to set.
		 * 													Default is PHPExcelBorderStyle::BORDER_THIN.
		 * @param      bool					$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  			    $col to be a numeric value. If this value is true
		 *			 										this funcition simply concatenates $col and $row
		 *													Default is false.
		 */
		public function SetBorders($col = 0, $row = 0, $isCoordinate = false, $borderSides = PHPExcelBorderType::All, $borderType = PHPExcelBorderStyle::BORDER_THIN) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$WholeRow = ($col == 0); $WholeCol = ($row == 0);
			// getHighsetColumn returns letter (AZ), need to convert to num
			if($col == 0) { $col = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn()); }
			if ($row == 0) { $row = $this->PhpXl->getActiveSheet()->getHighestRow(); }
			$CellCoordEnd = $this->GetCellCoord($col, $row, $isCoordinate);
			$CellCoordStart = 'A1';
			if (!$WholeCol || !$WholeRow) { // Only fall in here if one of them is false (Which means don't do all cells)
				if ($WholeCol) { $CellCoordStart = $this->GetCellCoord($col, 1, $isCoordinate); }
				if ($WholeRow) { $CellCoordStart = 'A'.$row; }
			}
			$Style = array(
				'borders' => array (
					$borderSides => array (
						'style' => $borderType
					)
				)
			);
			$CellRange = $CellCoordStart.':'.$CellCoordEnd;
			if ($CellCoordStart == $CellCoordEnd || (!$WholeCol && !$WholeRow)) { $CellRange = $CellCoordEnd; }
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->applyFromArray($Style);
			unset($Style); // freeup the memory
		}

		public function SetHTextAlign($col = 0, $row = 0, $isCoordinate = false, $align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$WholeRow = ($col == 0); $WholeCol = ($row == 0);
			// getHighsetColumn returns letter (AZ), need to convert to num
			if($col == 0) { $col = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn()); }
			if ($row == 0) { $row = $this->PhpXl->getActiveSheet()->getHighestRow(); }
			$CellCoordEnd = $this->GetCellCoord($col, $row, $isCoordinate);
			$CellCoordStart = 'A1';
			if (!$WholeCol || !$WholeRow) { // Only fall in here if one of them is false (Which means don't do all cells)
				if ($WholeCol) { $CellCoordStart = $this->GetCellCoord($col, 1, $isCoordinate); }
				if ($WholeRow) { $CellCoordStart = 'A'.$row; }
			}
			$CellRange = $CellCoordStart.':'.$CellCoordEnd;
			if ($CellCoordStart == $CellCoordEnd || (!$WholeCol && !$WholeRow)) { $CellRange = $CellCoordEnd; }
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getAlignment()->setHorizontal($align);
		}

		public function SetVTextAlign($col = 0, $row = 0, $isCoordinate = false, $align = PHPExcel_Style_Alignment::VERTICAL_CENTER) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$WholeRow = ($col == 0); $WholeCol = ($row == 0);
			// getHighsetColumn returns letter (AZ), need to convert to num
			if($col == 0) { $col = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn()); }
			if ($row == 0) { $row = $this->PhpXl->getActiveSheet()->getHighestRow(); }
			$CellCoordEnd = $this->GetCellCoord($col, $row, $isCoordinate);
			$CellCoordStart = 'A1';
			if (!$WholeCol || !$WholeRow) { // Only fall in here if one of them is false (Which means don't do all cells)
				if ($WholeCol) { $CellCoordStart = $this->GetCellCoord($col, 1, $isCoordinate); }
				if ($WholeRow) { $CellCoordStart = 'A'.$row; }
			}
			$CellRange = $CellCoordStart.':'.$CellCoordEnd;
			if ($CellCoordStart == $CellCoordEnd || (!$WholeCol && !$WholeRow)) { $CellRange = $CellCoordEnd; }
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getAlignment()->setVertical($align);
		}

		/**
		 * Set the background color of a cell
		 * 
		 * @param      int		$col   			The column of the cell to set the back color
		 * @param      int		$row   			The row of the cell to set the back color
		 * @param      string	$rgb   			The HTML based RGB color (e.g. 'FF0000' is red)
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  	$col to be a numeric value. If this value is true
		 *			 							this funcition simply concatenates $col and $row
		 *										Default is false.
		 */
		public function SetCellBackColor($col, $row, $rgb, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$CellCoord = $this->GetCellCoord($col, $row, $isCoordinate);
			$this->PhpXl->getActiveSheet()->getStyle($CellCoord)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->PhpXl->getActiveSheet()->getStyle($CellCoord)->getFill()->getStartColor()->setARGB('FF'.$rgb);
		}
		
		/**
		 * Set the text color of a cell
		 * 
		 * @param      int		$col   			The column of the cell to set the text color
		 * @param      int		$row   			The row of the cell to set the text color
		 * @param      string	$rgb   			The HTML based RGB color (e.g. 'FF0000' is red)
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  	$col to be a numeric value. If this value is true
		 *			 							this funcition simply concatenates $col and $row
		 *										Default is false.
		 */
		public function SetCellTextColor($col, $row, $rgb, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$CellCoord = $this->GetCellCoord($col, $row, $isCoordinate);
			$this->PhpXl->getActiveSheet()->getStyle($CellCoord)->getFont()->getColor()->setRGB($rgb);
		}

		/**
		 * Set the font name and size of a cell
		 *
		 * If $col and $row are set to 0 (their default values), then the entire
		 * active sheet is set to the font family ans size
		 * 
		 * @param      string	$fontName   	The font family name to set (e.g. 'Arial', 'Calibri', etc.)
		 * 										The font name must be a valid font name to set
		 * @param      int		$fontSize   	The font size to set (e.g. 10, 12, etc.)
		 * @param      int		$col   			(OPTIONAL) The column to set the font on.
		 * 										Default is 0. (0 means all columns)
		 * @param      int		$row   			(OPTIONAL) The row to set the font on.
		 * 										Default is 0. (0 means all rows)
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  	$col to be a numeric value. If this value is true
		 *			 							this funcition simply concatenates $col and $row
		 *										Default is false.
		 */
		public function SetCellFont($fontName, $fontSize, $bold = false, $col = 0, $row = 0, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$WholeRow = ($col == 0); $WholeCol = ($row == 0);
			// getHighsetColumn returns letter (AZ), need to convert to num
			if($col == 0) { $col = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn()); }
			if ($row == 0) { $row = $this->PhpXl->getActiveSheet()->getHighestRow(); }
			$CellCoordEnd = $this->GetCellCoord($col, $row, $isCoordinate);
			$CellCoordStart = 'A1';
			if (!$WholeCol || !$WholeRow) { // Only fall in here if one of them is false (Which means don't do all cells)
				if ($WholeCol) { $CellCoordStart = $this->GetCellCoord($col, 1, $isCoordinate); }
				if ($WholeRow) { $CellCoordStart = 'A'.$row; }
			}
			$CellRange = $CellCoordStart.':'.$CellCoordEnd;
			if ($CellCoordStart == $CellCoordEnd || (!$WholeCol && !$WholeRow)) { $CellRange = $CellCoordEnd; }
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getFont()->setName($fontName);
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getFont()->setSize($fontSize);
			$this->PhpXl->getActiveSheet()->getStyle($CellRange)->getFont()->setBold($bold);
			unset($Style); // freeup the memory
		}
		
		/**
		 * Sets the color of a column
		 * 
		 * @param      int			$col   The column to set the color to
		 * @param      string		$rgb   The HTML based RGB color (e.g. 'FF0000' is red)
		 */
		public function SetColumnColor($col, $rgb) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$LastRow = $this->PhpXl->getActiveSheet()->getHighestRow();
			for ($i = 1; $i <= $LastRow; $i++) {
				$this->SetCellBackColor($col, $i, $rgb);
			}
		}
		
		/**
		 * Set a column to certain font family and size
		 * 
		 * @param      int		$col   			The column to set the font on
		 * @param      string	$fontName   	The font family name to set (e.g. 'Arial', 'Calibri', etc.)
		 * 										The font name must be a valid font name to set
		 * @param      int		$fontSize   	The font size to set (e.g. 10, 12, etc.)
		 */
		public function SetColumnFont($col, $fontName, $fontSize) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$LastRow = $this->PhpXl->getActiveSheet()->getHighestRow();
			for ($i = 1; $i <= $LastRow; $i++) {
				$this->SetCellFont($fontName, $fontSize, $col, $i);
			}
		}
		
		/**
		 * Add a hyperlink to a cell
		 *
		 * When adding a hyperlink to a cell it does not color and underline
		 * the cell as if you were in Excel, to emulate this, set $autoColor = true
		 * 
		 * @param      int		$col   			The column of the cell
		 * @param      int		$row   			The row of the cell
		 * @param      string	$link   		The link to set the cell to
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                             			$col to be a numeric value. If this value is true
		 *			 							this funcition simply concatenates $col and $row
		 *										Default is false.
		 * @param      bool		$autoColor   	(OPTIONAL) True to emulate the coloring of a cell
		 *										Default is false.
		 */
		public function SetHyperlink($col, $row, $link, $isCoordinate = false, $autoColor = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$CellCoord = $this->GetCellCoord($col, $row, $isCoordinate);
			$Hyperlink = new PHPExcel_Cell_Hyperlink($link, '');
			$this->PhpXl->getActiveSheet()->setHyperlink($CellCoord, $Hyperlink);
			if ($autoColor) {
				$this->SetCellTextColor($col, $row, '0000FF', $isCoordinate);
			}
		}
		
		/**
		 * Set the current worksheet name
		 *
		 * This will set the name of the worksheet. You can see the name of
		 * the worksheet at the bottom of the Excel window (normally on
		 * a new worksheet it just says 'Sheet1')
		 * 
		 * @param      string	$name   The name of the sheet to set to
		 */
		public function SetWorksheetName($name) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$this->PhpXl->getActiveSheet()->setTitle($name);
		}
		
		/**
		 * Set an entire row to a certain color
		 * 
		 * @param      int		$row   The row to set the color to
		 * @param      string	$rgb   The HTML based RGB color (e.g. 'FF0000' is red)
		 */
		public function SetRowColor($row, $rgb) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$this->PhpXl->getActiveSheet()->getStyle('A'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->PhpXl->getActiveSheet()->getStyle('A'.$row)->getFill()->getStartColor()->setARGB('FF'.$rgb);
			$LastColumn = $this->PhpXl->getActiveSheet()->getHighestColumn(); // B
			$this->PhpXl->getActiveSheet()->duplicateStyle($this->PhpXl->getActiveSheet()->getStyle('A'.$row), 'B'.$row.':'.$LastColumn.$row);
		}
		
		/**
		 * Sets the direction of text in a cell
		 * 
		 * @param      int						$col   			The column of the cell to set the text direction
		 * @param      int						$row   			The row of the cell to set the text directoin
		 * @param      PHPExcelTextDirection	$dir   			The PHPExcelTextDirection to set
		 * @param      int						$angle   		The angle to set the text to
		 * @param      bool						$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  				    $col to be a numeric value. If this value is true
		 *			 											this funcition simply concatenates $col and $row
		 *														Default is false.
		 */
		public function SetCellTextDirection($col, $row, $dir, $angle, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$CellCoord = $this->GetCellCoord($col, $row, $isCoordinate);
			$angle = abs($angle);
			switch ($dir) {
				case PHPExcelTextDirection::Clockwise:
					$angle = -$angle;
					break;
				case PHPExcelTextDirection::CounterClockwise:
					// Do nothing since clockwise rotation is a positive value
					break;
				case PHPExcelTextDirection::Stacked:
					$angle = -165; // Stacked text ALWAYS has to be angle 165
					break;
				default:
					// Do we need to do anything here?? this will essentually be CounterClockwise
					break;
			}
			$this->PhpXl->getActiveSheet()->getStyle($CellCoord)->getAlignment()->setTextRotation($angle);
		}
		
		/**
		 * Sets the font of a row
		 * 
		 * @param      int		$row   			The row to set the font to
		 * @param      string	$fontName   	The font family name to set (e.g. 'Arial', 'Calibri', etc.)
		 * 										The font name must be a valid font name to set
		 * @param      int		$fontSize   	The font size to set (e.g. 10, 12, etc.)
		 */
		public function SetRowFont($row, $fontName, $fontSize) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			$LastColumn = $this->GetExcelColumnFromAlpha($this->PhpXl->getActiveSheet()->getHighestColumn());
			for ($i = 1; $i <= $LastColumn; $i++) {
				$this->SetCellFont($fontName, $fontSize, $i, $row);
			}
		}
		
		/**
		 * Write data to a specific cell
		 * 
		 * @param      int		$col   			The column of the cell to write to
		 * @param      int		$row   			The row of the cell to write to
		 * @param      mixed	$data   		The data to write to the cell
		 * @param      bool		$isCoordinate   (OPTIONAL) If this is false, the function expects
		 *                                  	$col to be a numeric value. If this value is true
		 *			 							this funcition simply concatenates $col and $row
		 *										Default is false.
		 */
		public function WriteCell($col, $row, $data, $isCoordinate = false) {
			if ($this->Type == PHPExcelWrapperType::CSV) { return; }
			if ($isCoordinate) {
				$this->PhpXl->getActiveSheet()->setCellValue(($col.$row), $data);
			} else {
				$this->PhpXl->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data);
			}
			//$this->Flush();
		}
		
		/**
		 * Writes a row of data an advances the current row pointer
		 * 
		 * @param      mixed	$data   The data to write (can be an array)
		 */
		public function Write($data) {
			if ($this->Type == PHPExcelWrapperType::CSV) {
				if (is_array($data)) { $Splits = $data; }
				else { $Splits = explode(',', $data); }
				$this->WriteRow($this->CurrentRow++, $Splits);
			} else {
				$this->WriteRow($this->CurrentRow++, $data);
			}
		}
		
		/**
		 * Writes data to a specific row
		 * 
		 * @param      int		$row   	The row to write the data to
		 * @param      mixed	$data   The data to write
		 */
		public function WriteRow($row, $data) {
			if ($this->Type == PHPExcelWrapperType::CSV) {
				$OutValue = $data;
				if (is_array($data)) {
					$OutValue = '';
					$Count = count($data);
					for ($i = 0; $i < $Count; $i++) {
						$OutValue .= $data[$i];
						if ($i < $Count - 1) { $OutValue .= ','; }
					}
				}
				if (substr($OutValue, (strlen($OutValue) - 1), 1) != "\n") {
					$OutValue .= "\n";
				}
				fputs($this->Handle, $OutValue);
			} else {
				if (is_array($data)) {
					$Count = count($data);
					for ($i = 0; $i < $Count; $i++) {
						$this->WriteCell($i, $row, $data[$i]);
					}
				} else {
					$this->WriteCell($col, $row, $data);
				}
			}
		}
		
		/******************************END SET FUNCTIONS******************************/
	}
?>