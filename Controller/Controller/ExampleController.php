<?php

/**
* Every custom controllers must extend the IndexController in order to use
* Twig and render views.
* Every method must be ended by 'Action' suffix.
*/

namespace Controller\Controller;

class ExampleController extends IndexController
{
	public function helloWorldAction()
	{
		return $this->render('example/hello-world.html.twig');
	}

	public function helloAnyoneAction()
	{
		return $this->render('example/hello-anyone.html.twig', array(
			'name' => $this->request->getParam('anyone')
		));
	}
}
