<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('SassComponent', 'SassCompiler.Controller/Component');

// A fake controller to test against
class TestSassController extends Controller {

	public $paginate = null;
}


/**
 * SassComponent Test Case
 */
class SassComponentTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$Collection = new ComponentCollection();
		$this->Sass = new SassComponent($Collection);

		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new TestSassController($CakeRequest, $CakeResponse);

		$this->Sass->startup($this->Controller);
		$this->Sass->initialize($this->Controller);
	}

	public function testCleanGeneratedCss() {
		//Generate the CSS files
		$this->Sass->generateCss();

		//Clean the CSS files
		$cleanedFiles = $this->Sass->cleanGeneratedCss();

		foreach ($cleanedFiles as $path) {
			$this->assertFileNotExists($path);
		}
	}

	public function testGenerateCss() {
		//Clean all generated CSS files
		$this->Sass->cleanGeneratedCss();

		//Generate the css files
		$generatedFiles = $this->Sass->generateCss();

		foreach ($generatedFiles as $path) {
			$this->assertFileExists($path);
		}

		//Test generate of CSS with debug off and autorun off
		Configure::write('debug', 0);
		$Sass = new SassComponent(new ComponentCollection(), array('autoRun' => false));
		$Sass->startup($this->Controller);
		$Sass->initialize($this->Controller);

		//Clean all generated CSS files
		$this->Sass->cleanGeneratedCss();

		$generatedFiles = $Sass->generateCss();

		//Files may not be generated
		$this->assertEmpty($generatedFiles);

		Configure::write('debug', 2);
	}

	public function testSetFoldersFallback() {
		$Sass = new SassComponent(new ComponentCollection(), array('sourceFolder' => false));
		$Sass->startup($this->Controller);
		$Sass->initialize($this->Controller);

		$this->assertFalse($Sass->settings['sourceFolder']);
	}

	public function testBeforeRender() {
		//Clean all generated CSS files
		$this->Sass->cleanGeneratedCss();

		//Trigger beforeRender
		$this->Sass->beforeRender($this->Controller);

		//Check if the files are generated
		$generatedFiles = $this->Sass->generateCss();

		foreach ($generatedFiles as $path) {
			$this->assertFileExists($path);
		}
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Sass);

		parent::tearDown();
	}
}