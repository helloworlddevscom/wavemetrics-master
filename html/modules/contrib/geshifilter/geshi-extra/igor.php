<?php
/*************************************************************************************
 * SSD0:Users:aclight:Documents:scripts:igor.php
 * --------
 * Author: Adam Light (aclight@gmail.org)
 * Copyright: (c) 2004 Nigel McNie (http://qbnz.com/highlighter/)
 * Release Version: 1.0.7.18
 * Date Started: 2007/02/25
 *
 * IGOR language file for GeSHi.
 *
 * CURRENT LANGUAGE VERSION
 * Created on: Thu, Jul 29, 2021
 * Igor Version: 9.00B08
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = array (
  'LANG_NAME' => 'IGOR',
  'COMMENT_SINGLE' => array(1 => '//'),
  'COMMENT_MULTI' => array(),
  'CASE_KEYWORDS' => GESHI_CAPS_NO_CHANGE,
  'QUOTEMARKS' => array('"'),
  'ESCAPE_CHAR' => '\\',
  'KEYWORDS' => array(
    1 => array(
      'while', 'try', 'switch', 'strswitch',
      'return', 'if', 'for', 'endtry',
      'endswitch', 'endif', 'endfor', 'elseif',
      'else', 'do', 'continue', 'catch',
      'case', 'break'
    ),
    2 => array(
      'window', 'WAVE', 'variable', 'ThreadSafe',
      'SVAR', 'SubMenu', 'Structure', 'STRUCT',
      'string', 'strconstant', 'static', 'Prompt',
      'Proc', 'Picture', 'NVAR', 'MultiThread',
      'Menu', 'macro', 'graph', 'function',
      'FuncFit', 'EndStructure', 'EndMacro', 'end',
      'DoPrompt', 'constant'
    ),
    3 => array(
      'zeta', 'ZernikeR', 'zcsr', 'z',
      'y', 'XWaveRefFromTrace', 'XWaveName', 'xcsr',
      'x2pnt', 'x', 'wnoise', 'WinType',
      'WinRecreation', 'WinName', 'WinList', 'WhichListItem',
      'WaveUnits', 'WaveType', 'WaveTextEncoding', 'WaveRefWaveToList',
      'WaveRefsEqual', 'WaveRefIndexedDFR', 'WaveRefIndexed', 'WaveName',
      'WaveModCount', 'WaveMinAndMax', 'WaveMin', 'WaveMax',
      'WaveList', 'WaveInfo', 'WaveHash', 'WaveExists',
      'WaveDims', 'WaveDataToString', 'WaveCRC', 'VoigtPeak',
      'VoigtFunc', 'vcsr', 'Variance', 'VariableList',
      'URLEncode', 'URLDecode', 'UpperStr', 'UnsetEnvironmentVariable',
      'UnPadString', 'UniqueName', 'trunc', 'TrimString',
      'TraceNameToWaveRef', 'TraceNameList', 'TraceInfo', 'TraceFromPixel',
      'time', 'ticks', 'ThreadReturnValue', 'ThreadProcessorCount',
      'ThreadGroupWait', 'ThreadGroupRelease', 'ThreadGroupGetDFR', 'ThreadGroupGetDF',
      'ThreadGroupCreate', 'TextFile', 'TextEncodingName', 'TextEncodingCode',
      'tanh', 'tan', 'TagWaveRef', 'TagVal',
      'TableInfo', 't', 'SVAR_Exists', 'sum',
      'StudentT', 'StudentA', 'StrVarOrDefault', 'strsearch',
      'strlen', 'StringToUnsignedByteWave', 'stringmatch', 'StringList',
      'StringFromList', 'stringCRC', 'StringByKey', 'str2num',
      'StopMSTimer', 'StatsWeibullPDF', 'StatsWeibullCDF', 'StatsWaldPDF',
      'StatsWaldCDF', 'StatsVonMisesPDF', 'StatsVonMisesNoise', 'StatsVonMisesCDF',
      'StatsUSquaredCDF', 'StatsTrimmedMean', 'StatsTriangularPDF', 'StatsTriangularCDF',
      'StatsTopDownCDF', 'StatsStudentPDF', 'StatsStudentCDF', 'StatsSpearmanRhoCDF',
      'StatsRunsCDF', 'StatsRectangularPDF', 'StatsRectangularCDF', 'StatsRayleighPDF',
      'StatsRayleighCDF', 'StatsQpCDF', 'StatsQCDF', 'StatsPowerPDF',
      'StatsPowerNoise', 'StatsPowerCDF', 'StatsPoissonPDF', 'StatsPoissonCDF',
      'StatsPermute', 'StatsParetoPDF', 'StatsParetoCDF', 'StatsNormalPDF',
      'StatsNormalCDF', 'StatsNCTPDF', 'StatsNCTCDF', 'StatsNCFPDF',
      'StatsNCFCDF', 'StatsNCChiPDF', 'StatsNCChiCDF', 'StatsNBinomialPDF',
      'StatsNBinomialCDF', 'StatsMooreCDF', 'StatsMedian', 'StatsMaxwellPDF',
      'StatsMaxwellCDF', 'StatsLogNormalPDF', 'StatsLogNormalCDF', 'StatsLogisticPDF',
      'StatsLogisticCDF', 'StatsKuiperCDF', 'StatsInvWeibullCDF', 'StatsInvVonMisesCDF',
      'StatsInvUsquaredCDF', 'StatsInvTriangularCDF', 'StatsInvTopDownCDF', 'StatsInvStudentCDF',
      'StatsInvSpearmanCDF', 'StatsInvRectangularCDF', 'StatsInvRayleighCDF', 'StatsInvQpCDF',
      'StatsInvQCDF', 'StatsInvPowerCDF', 'StatsInvPoissonCDF', 'StatsInvParetoCDF',
      'StatsInvNormalCDF', 'StatsInvNCFCDF', 'StatsInvNCChiCDF', 'StatsInvNBinomialCDF',
      'StatsInvMooreCDF', 'StatsInvMaxwellCDF', 'StatsInvLogNormalCDF', 'StatsInvLogisticCDF',
      'StatsInvKuiperCDF', 'StatsInvGeometricCDF', 'StatsInvGammaCDF', 'StatsInvFriedmanCDF',
      'StatsInvFCDF', 'StatsInvExpCDF', 'StatsInvEValueCDF', 'StatsInvDExpCDF',
      'StatsInvCMSSDCDF', 'StatsInvChiCDF', 'StatsInvCauchyCDF', 'StatsInvBinomialCDF',
      'StatsInvBetaCDF', 'StatsHyperGPDF', 'StatsHyperGCDF', 'StatsGEVPDF',
      'StatsGEVCDF', 'StatsGeometricPDF', 'StatsGeometricCDF', 'StatsGammaPDF',
      'StatsGammaCDF', 'StatsFriedmanCDF', 'StatsFPDF', 'StatsFCDF',
      'StatsExpPDF', 'StatsExpCDF', 'StatsEValuePDF', 'StatsEValueCDF',
      'StatsErrorPDF', 'StatsErlangPDF', 'StatsErlangCDF', 'StatsDExpPDF',
      'StatsDExpCDF', 'StatsCorrelation', 'StatsCMSSDCDF', 'StatsChiPDF',
      'StatsChiCDF', 'StatsCauchyPDF', 'StatsCauchyCDF', 'StatsBinomialPDF',
      'StatsBinomialCDF', 'StatsBetaPDF', 'StatsBetaCDF', 'StartMSTimer',
      'sqrt', 'SphericalHarmonics', 'SphericalBessYD', 'SphericalBessY',
      'SphericalBessJD', 'SphericalBessJ', 'SpecialDirPath', 'SpecialCharacterList',
      'SpecialCharacterInfo', 'SortList', 'sinIntegral', 'sinh',
      'sinc', 'sin', 'sign', 'SetEnvironmentVariable',
      'SelectString', 'SelectNumber', 'Secs2Time', 'Secs2Date',
      'sech', 'sec', 'ScreenResolution', 'scaleToIndex',
      'sawtooth', 's', 'round', 'rightx',
      'ReplicateString', 'ReplaceStringByKey', 'ReplaceString', 'ReplaceNumberByKey',
      'RemoveListItem', 'RemoveFromList', 'RemoveEnding', 'RemoveByKey',
      'real', 'r2polar', 'r', 'qcsr',
      'q', 'ProcedureText', 'PossiblyQuoteName', 'PolygonArea',
      'poly2D', 'poly', 'poissonNoise', 'pnt2x',
      'PixelFromAxisVal', 'PICTList', 'PICTInfo', 'Pi',
      'pcsr', 'PathList', 'ParseFilePath', 'ParamIsDefault',
      'PanelResolution', 'PadString', 'p2rect', 'p',
      'OperationList', 'NVAR_Exists', 'NumVarOrDefault', 'numtype',
      'numpnts', 'NumberByKey', 'num2str', 'num2istr',
      'num2char', 'note', 'NormalizeUnicode', 'norm',
      'NewFreeWave', 'NewFreeDataFolder', 'NaN', 'NameOfWave',
      'MPFXVoigtPeak', 'MPFXLorentzianPeak', 'MPFXGaussPeak', 'MPFXExpConvExpPeak',
      'MPFXEMGPeak', 'ModDate', 'mod', 'min',
      'median', 'mean', 'max', 'MatrixTrace',
      'MatrixRank', 'MatrixDot', 'MatrixDet', 'MatrixCondition',
      'MarcumQ', 'MandelbrotPoint', 'magsqr', 'MacroList',
      'LowerStr', 'lorentzianNoise', 'logNormalNoise', 'log',
      'ln', 'ListToWaveRefWave', 'ListToTextWave', 'ListMatch',
      'limit', 'LegendreA', 'leftx', 'LayoutInfo',
      'LambertW', 'LaguerreGauss', 'LaguerreA', 'Laguerre',
      'JulianToDate', 'JacobiSn', 'JacobiCn', 'j',
      'ItemsInList', 'inverseERFC', 'inverseERF', 'Interp3D',
      'Interp2D', 'interp', 'Integrate1D', 'Inf',
      'IndexToScale', 'IndexedFile', 'IndexedDir', 'IndependentModuleList',
      'ImageNameToWaveRef', 'ImageNameList', 'ImageInfo', 'imag',
      'IgorVersion', 'IgorInfo', 'i', 'HyperGPFQ',
      'HyperGNoise', 'HyperG2F1', 'HyperG1F1', 'HyperG0F1',
      'hermiteGauss', 'hermite', 'HDF5TypeInfo', 'HDF5LinkInfo',
      'HDF5LibraryInfo', 'HDF5DatasetInfo', 'HDF5AttributeInfo', 'hcsr',
      'Hash', 'GuideNameList', 'GuideInfo', 'GrepString',
      'GrepList', 'gnoise', 'GizmoScale', 'GizmoInfo',
      'GetWindowBrowserSelection', 'GetWavesDataFolderDFR', 'GetWavesDataFolder', 'GetUserData',
      'GetScrapText', 'GetRTStackInfo', 'GetRTLocInfo', 'GetRTLocation',
      'GetRTError', 'GetRTErrMessage', 'GetKeyState', 'GetIndexedObjNameDFR',
      'GetIndexedObjName', 'GetIndependentModuleName', 'GetFormula', 'GetErrMessage',
      'GetEnvironmentVariable', 'GetDimLabel', 'GetDefaultFontStyle', 'GetDefaultFontSize',
      'GetDefaultFont', 'GetDataFolderDFR', 'GetDataFolder', 'GetBrowserSelection',
      'GetBrowserLine', 'GeometricMean', 'gcd', 'Gauss2D',
      'Gauss1D', 'Gauss', 'gammq', 'gammp',
      'gammln', 'gammaNoise', 'gammaInc', 'gammaEuler',
      'gamma', 'FunctionPath', 'FunctionList', 'FunctionInfo',
      'FuncRefInfo', 'FresnelSin', 'FresnelCos', 'FontSizeStringWidth',
      'FontSizeHeight', 'FontList', 'floor', 'FindListItem',
      'FindDimLabel', 'FetchURL', 'faverageXY', 'faverage',
      'fakedata', 'Faddeeva', 'factorial', 'expNoise',
      'expIntegralE1', 'expInt', 'exp', 'exists',
      'erfcw', 'erfc', 'erf', 'equalWaves',
      'enoise', 'ellipticK', 'ellipticE', 'ei',
      'e', 'DimSize', 'DimOffset', 'DimDelta',
      'dilogarithm', 'digamma', 'deltax', 'defined',
      'Dawson', 'DateToJulian', 'datetime', 'date2secs',
      'date', 'DataFolderRefStatus', 'DataFolderRefsEqual', 'DataFolderRefChanges',
      'DataFolderList', 'DataFolderExists', 'DataFolderDir', 'CTabList',
      'CsrXWaveRef', 'CsrXWave', 'CsrWaveRef', 'CsrWave',
      'CsrInfo', 'csch', 'csc', 'CreationDate',
      'CreateDataObjectName', 'cpowi', 'CountObjectsDFR', 'CountObjects',
      'coth', 'cot', 'cosIntegral', 'cosh',
      'cos', 'ConvertTextEncoding', 'ControlNameList', 'ContourZ',
      'ContourNameToWaveRef', 'ContourNameList', 'ContourInfo', 'conj',
      'cmpstr', 'cmplx', 'CleanupName', 'ChildWindowList',
      'CheckName', 'chebyshevU', 'chebyshev', 'char2num',
      'cequal', 'centerOfMassXY', 'centerOfMass', 'ceil',
      'CaptureHistoryStart', 'CaptureHistory', 'cabs', 'binomialNoise',
      'binomialln', 'binomial', 'BinarySearchInterp', 'BinarySearch',
      'betai', 'beta', 'Bessely', 'Besselk',
      'Besselj', 'Besseli', 'Base64Encode', 'Base64Decode',
      'AxisValFromPixel', 'AxisList', 'AxisLabel', 'AxisInfo',
      'atanh', 'atan2', 'atan', 'asinh',
      'asin', 'areaXY', 'area', 'AnnotationList',
      'AnnotationInfo', 'alog', 'AiryBD', 'AiryB',
      'AiryAD', 'AiryA', 'AddListItem', 'acosh',
      'acos', 'abs'
    ),
    4 => array(
      'XLLoadWave', 'WindowFunction', 'WignerTransform', 'wfprintf',
      'WaveTransform', 'WaveTracking', 'WaveStats', 'WaveMeanStdv',
      'Variable', 'ValDisplay', 'URLRequest', 'UnzipFile',
      'Unwrap', 'Triangulate3d', 'ToolsGrid', 'ToCommandLine',
      'TitleBox', 'TileWindows', 'Tile', 'TickWavesFromAxis',
      'ThreadStart', 'ThreadGroupPutDF', 'TextHistogram', 'TextBox',
      'Text2Bezier', 'Tag', 'TabControl', 'SumSeries',
      'SumDimension', 'StructPut', 'StructGet', 'StructFill',
      'String', 'STFT', 'StatsWRCorrelationTest', 'StatsWilcoxonRankTest',
      'StatsWheelerWatsonTest', 'StatsWatsonWilliamsTest', 'StatsWatsonUSquaredTest', 'StatsVariancesTest',
      'StatsTukeyTest', 'StatsTTest', 'StatsSRTest', 'StatsSignTest',
      'StatsShapiroWilkTest', 'StatsScheffeTest', 'StatsSample', 'StatsResample',
      'StatsRankCorrelationTest', 'StatsQuantiles', 'StatsNPNominalSRTest', 'StatsNPMCTest',
      'StatsMultiCorrelationTest', 'StatsLinearRegression', 'StatsLinearCorrelationTest', 'StatsKWTest',
      'StatsKSTest', 'StatsKendallTauTest', 'StatsKDE', 'StatsJBTest',
      'StatsHodgesAjneTest', 'StatsFTest', 'StatsFriedmanTest', 'StatsDunnettTest',
      'StatsDIPTest', 'StatsContingencyTable', 'StatsCochranTest', 'StatsCircularTwoSampleTest',
      'StatsCircularMoments', 'StatsCircularMeans', 'StatsCircularCorrelationTest', 'StatsChiTest',
      'StatsANOVA2Test', 'StatsANOVA2RMTest', 'StatsANOVA2NRTest', 'StatsANOVA1Test',
      'StatsAngularDistanceTest', 'StackWindows', 'Stack', 'sscanf',
      'sprintf', 'SplitWave', 'SplitString', 'SphericalTriangulate',
      'SphericalInterpolate', 'SoundSaveWave', 'SoundLoadWave', 'SoundInStopChart',
      'SoundInStatus', 'SoundInStartChart', 'SoundInSet', 'SoundInRecord',
      'SortColumns', 'Sort', 'SmoothCustom', 'Smooth',
      'Slider', 'Sleep', 'Silent', 'ShowTools',
      'ShowInfo', 'ShowIgorMenus', 'SetWindow', 'SetWaveTextEncoding',
      'SetWaveLock', 'SetVariable', 'SetScale', 'SetRandomSeed',
      'SetProcessSleep', 'SetMarquee', 'SetIgorOption', 'SetIgorMenuMode',
      'SetIgorHook', 'SetIdlePeriod', 'SetFormula', 'SetFileFolderInfo',
      'SetDrawLayer', 'SetDrawEnv', 'SetDimLabel', 'SetDataFolder',
      'SetDashPattern', 'SetBackground', 'SetAxis', 'SetActiveSubwindow',
      'SaveTableCopy', 'SavePICT', 'SavePackagePreferences', 'SaveNotebook',
      'SaveGraphCopy', 'SaveGizmoCopy', 'SaveExperiment', 'SaveData',
      'Save', 'Rotate', 'Reverse', 'ResumeUpdate',
      'Resample', 'ReplaceWave', 'ReplaceText', 'ReorderTraces',
      'ReorderImages', 'RenameWindow', 'RenamePICT', 'RenamePath',
      'RenameDataFolder', 'Rename', 'RemovePath', 'RemoveLayoutObjects',
      'RemoveImage', 'RemoveFromTable', 'RemoveFromLayout', 'RemoveFromGraph',
      'RemoveFromGizmo', 'RemoveContour', 'Remove', 'Remez',
      'Redimension', 'RatioFromNumber', 'Quit', 'pwd',
      'PutScrapText', 'PulseStats', 'Project', 'PrintTable',
      'PrintSettings', 'PrintNotebook', 'PrintLayout', 'PrintGraphs',
      'printf', 'Print', 'PrimeFactors', 'Preferences',
      'PopupMenu', 'PopupContextualMenu', 'PolygonOp', 'PlaySound',
      'PlayMovieAction', 'PlayMovie', 'PCA', 'PauseUpdate',
      'PauseForUser', 'PathInfo', 'ParseOperationTemplate', 'Optimize',
      'OpenNotebook', 'OpenHelp', 'Open', 'NotebookAction',
      'Notebook', 'Note', 'NewWaterfall', 'NewPath',
      'NewPanel', 'NewNotebook', 'NewMovie', 'NewLayout',
      'NewImage', 'NewGizmo', 'NewFreeAxis', 'NewFIFOChan',
      'NewFIFO', 'NewDataFolder', 'NewCamera', 'NeuralNetworkTrain',
      'NeuralNetworkRun', 'MultiThreadingControl', 'MultiTaperPSD', 'MoveWindow',
      'MoveWave', 'MoveVariable', 'MoveSubwindow', 'MoveString',
      'MoveFolder', 'MoveFile', 'MoveDataFolder', 'ModifyWaterfall',
      'ModifyViolinPlot', 'ModifyTable', 'ModifyProcedure', 'ModifyPanel',
      'ModifyLayout', 'ModifyImage', 'ModifyGraph', 'ModifyGizmo',
      'ModifyFreeAxis', 'ModifyControlList', 'ModifyControl', 'ModifyContour',
      'ModifyCamera', 'ModifyBrowser', 'ModifyBoxPlot', 'Modify',
      'MLLoadWave', 'MeasureStyledText', 'MatrixTranspose', 'MatrixSVD',
      'MatrixSVBkSub', 'MatrixSparse', 'MatrixSolve', 'MatrixSchur',
      'MatrixReverseBalance', 'MatrixOP', 'MatrixMultiplyAdd', 'MatrixMultiply',
      'MatrixLUDTD', 'MatrixLUD', 'MatrixLUBkSub', 'MatrixLLS',
      'MatrixLinearSolveTD', 'MatrixLinearSolve', 'MatrixInverse', 'MatrixGLM',
      'MatrixGaussJ', 'MatrixFilter', 'MatrixFactor', 'MatrixEigenV',
      'MatrixCorr', 'MatrixConvolve', 'MatrixBalance', 'MarkPerfTestTime',
      'MakeIndex', 'Make', 'LombPeriodogram', 'Loess',
      'LoadWave', 'LoadPICT', 'LoadPackagePreferences', 'LoadData',
      'ListBox', 'LinearFeedbackShiftRegister', 'Legend', 'LayoutSlideShow',
      'LayoutPageAction', 'Layout', 'Label', 'KMeans',
      'KillWindow', 'KillWaves', 'KillVariables', 'KillStrings',
      'KillPICTs', 'KillPath', 'KillFreeAxis', 'KillFIFO',
      'KillDataFolder', 'KillControl', 'KillBackground', 'JointHistogram',
      'JCAMPLoadWave', 'Interpolate3D', 'Interpolate2', 'Interp3DPath',
      'IntegrateODE', 'Integrate2D', 'Integrate', 'InstantFrequency',
      'InsertPoints', 'IndexSort', 'ImageWindow', 'ImageUnwrapPhase',
      'ImageTransform', 'ImageThreshold', 'ImageStats', 'ImageSnake',
      'ImageSkeleton3d', 'ImageSeedFill', 'ImageSave', 'ImageRotate',
      'ImageRestore', 'ImageRemoveBackground', 'ImageRegistration', 'ImageMorphology',
      'ImageLoad', 'ImageLineProfile', 'ImageInterpolate', 'ImageHistogram',
      'ImageHistModification', 'ImageGLCM', 'ImageGenerateROIMask', 'ImageFromXYZ',
      'ImageFocus', 'ImageFilter', 'ImageFileInfo', 'ImageEdgeDetection',
      'ImageComposite', 'ImageBoundaryToMask', 'ImageBlend', 'ImageAnalyzeParticles',
      'IFFT', 'ICA', 'Histogram', 'HilbertTransform',
      'HideTools', 'HideProcedures', 'HideInfo', 'HideIgorMenus',
      'HDF5UnlinkObject', 'HDF5SaveImage', 'HDF5SaveGroup', 'HDF5SaveData',
      'HDF5OpenGroup', 'HDF5OpenFile', 'HDF5LoadImage', 'HDF5LoadGroup',
      'HDF5LoadData', 'HDF5ListGroup', 'HDF5ListAttributes', 'HDF5FlushFile',
      'HDF5DumpErrors', 'HDF5Dump', 'HDF5DimensionScale', 'HDF5CreateLink',
      'HDF5CreateGroup', 'HDF5CreateFile', 'HDF5Control', 'HDF5CloseGroup',
      'HDF5CloseFile', 'HCluster', 'Hanning', 'GroupBox',
      'Grep', 'GraphWaveEdit', 'GraphWaveDraw', 'GraphNormal',
      'GetWindow', 'GetSelection', 'GetMouse', 'GetMarquee',
      'GetLastUserMenuInfo', 'GetGizmo', 'GetFileFolderInfo', 'GetCamera',
      'GetAxis', 'GBLoadWave', 'FuncFitMD', 'FuncFit',
      'FTPUpload', 'FTPDownload', 'FTPDelete', 'FTPCreateDirectory',
      'FStatus', 'FSetPos', 'FReadLine', 'fprintf',
      'FPClustering', 'FMaxFlat', 'FindValue', 'FindSequence',
      'FindRoots', 'FindPointsInPoly', 'FindPeak', 'FindLevels',
      'FindLevel', 'FindDuplicates', 'FindContour', 'FindAPeak',
      'FilterIIR', 'FilterFIR', 'FIFOStatus', 'FIFO2Wave',
      'FGetPos', 'FFT', 'FBinWrite', 'FBinRead',
      'FastOp', 'FastGaussTransform', 'Extract', 'ExportGizmo',
      'ExperimentModified', 'ExperimentInfo', 'ExecuteScriptText', 'Execute',
      'EstimatePeakSizes', 'ErrorBars', 'Edit', 'EdgeStats',
      'DWT', 'DuplicateDataFolder', 'Duplicate', 'DSPPeriodogram',
      'DSPDetrend', 'DrawUserShape', 'DrawText', 'DrawRRect',
      'DrawRect', 'DrawPoly', 'DrawPICT', 'DrawOval',
      'DrawLine', 'DrawBezier', 'DrawArc', 'DrawAction',
      'DPSS', 'DoXOPIdle', 'DoWindow', 'DoUpdate',
      'DoIgorMenu', 'DoAlert', 'DisplayProcedure', 'DisplayHelpTopic',
      'Display', 'dir', 'Differentiate', 'DeletePoints',
      'DeleteFolder', 'DeleteFile', 'DeleteAnnotations', 'DelayUpdate',
      'DefineGuide', 'DefaultTextEncoding', 'DefaultGuiFont', 'DefaultGuiControls',
      'DefaultFont', 'DebuggerOptions', 'Debugger', 'CWT',
      'CustomControl', 'CurveFit', 'Cursor', 'CtrlNamedBackground',
      'CtrlFIFO', 'CtrlBackground', 'Cross', 'CreateBrowser',
      'CreateAliasShortcut', 'Correlate', 'CopyScales', 'CopyFolder',
      'CopyFile', 'CopyDimLabels', 'Convolve', 'ConvexHull',
      'ConvertGlobalStringTextEncoding', 'ControlUpdate', 'ControlInfo', 'ControlBar',
      'Concatenate', 'ColorTab2Wave', 'ColorScale', 'CloseProc',
      'CloseMovie', 'CloseHelp', 'Close', 'ChooseColor',
      'CheckDisplayed', 'CheckBox', 'Chart', 'cd',
      'Button', 'BuildMenu', 'BrowseURL', 'BoxSmooth',
      'BoundingBall', 'BezierToPolygon', 'Beep', 'BackgroundInfo',
      'AutoPositionWindow', 'AppendXYZContour', 'AppendViolinPlot', 'AppendToTable',
      'AppendToLayout', 'AppendToGraph', 'AppendToGizmo', 'AppendText',
      'AppendMatrixContour', 'AppendLayoutObject', 'AppendImage', 'AppendBoxPlot',
      'Append', 'APMath', 'AdoptFiles', 'AddWavesToViolinPlot',
      'AddWavesToBoxPlot', 'AddMovieFrame', 'AddMovieAudio', 'AddFIFOVectData',
      'AddFIFOData', 'Abort'
    ),
    5 => array(
      '#pragma', '#include'
    ),
  ),
  'SYMBOLS' => array(
    '(', ')', '[', ']', '{', '}', '!', '@', '%', '&', '*', '|', '/', '<', '>'
  ),
  'CASE_SENSITIVE' => array(
    GESHI_COMMENTS => false,
    1 => false,
    2 => false,
    3 => false,
    4 => false,
    5 => false
  ),
  'STYLES' => array(
    'KEYWORDS' => array(
      1 => 'color: #0000ff;',
      2 => 'color: #0000ff;',
      3 => 'color: #c34e00;',
      4 => 'color: #007575;',
      5 => 'color: #cc00a3;'
    ),
    'COMMENTS' => array(
      1 => 'color: #ff0000; font-style: italic;',
    ),
    'ESCAPE_CHAR' => array(
      0 => 'color: #009c00;'
    ),
    'BRACKETS' => array(
      0 => 'color: #000000;'
    ),
    'STRINGS' => array(
      0 => 'color: #009c00;'
    ),
    'NUMBERS' => array(
      0 => 'color: #000000;'
    ),
    'METHODS' => array(
      0 => 'color: #000000;'
    ),
    'SYMBOLS' => array(
      0 => 'color: #000000;'
    ),
    'REGEXPS' => array(
      0 => 'color: #000000;'
    ),
    'SCRIPT' => array(
      0 => '',
      1 => '',
      2 => '',
      3 => ''
    )
  ),
  'URLS' => array(
    1 => '',
    2 => '',
    3 => '',
    4 => '',
    5 => ''
  ),
  'OOLANG' => false,
  'OBJECT_SPLITTERS' => array(
    1 => '',
    2 => ''
  ),
  'REGEXPS' => array(
    0 => "[\$]{1,2}[a-zA-Z_][a-zA-Z0-9_]*",
    1 => array(
      GESHI_SEARCH  => "([a-zA-Z]+)(\n)(.*)(\n)(\1;?)",
      GESHI_REPLACE => '\3',
      GESHI_BEFORE => '\1\2',
      GESHI_AFTER => '\4\5',
      GESHI_MODIFIERS => 'siU'
    )
  ),
  'STRICT_MODE_APPLIES' => GESHI_NEVER,
  'SCRIPT_DELIMITERS' => array(
    0 => array(
      '<?php' => '?>'
    ),
    1 => array(
      '<?' => '?>'
    ),
    2 => array(
      '<%' => '%>'
    ),
    3 => array(
      '<script language="igor">' => '</script>'
    )
  ),
  'HIGHLIGHT_STRICT_BLOCK' => array(
    0 => true,
    1 => true,
    2 => true,
    3 => true,
    4 => true,
    5 => true
  )
);
