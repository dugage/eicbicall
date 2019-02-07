<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plantillas extends MX_Controller
{
    private $nameClass;
    private $icono;
    private $proyecto;

	public function __construct()
	{
		parent::__construct();
        $this->nameClass = get_class($this);
        $this->proyecto = $this->doctrine->em->find("Entities\\Proyecto", 1);
        $this->icono = 'fa fa-envelope-o';
        //helper uploads
        $this->load->helper('upload_helper');
	}

	public function index()
	{
        //pasamos los datos básicos del template
        $data['lang'] = "es";
        $data['title'] = $this->proyecto->getNombre()." | Panel de control";
        $data['view'] = strtolower(__FUNCTION__."_".$this->nameClass);
        $data['robots'] = 'noindex, nofollow';
        $data['project'] = $this->proyecto;
        $data['reference'] = strtoupper(__FUNCTION__."-".$this->nameClass);
        //icono del módulo
        $data['icono'] = $this->icono;
        // titulo del módulo
        $data['h1'] = $this->nameClass;
        //lista migas pan
        $data['breadcrumb'] = array($this->nameClass);
        //datos cabecera tabla
        $data['thead'] = array('ID','Título');
        //ruta para los botones y acciones
        $data['path'] = $this->uri->segment(1).'/'.$this->uri->segment(2);

        //obtenemos y mostramos todos los datos
        $data['getResult'] = $this->doctrine->em->getRepository("Entities\\Templates")->findAll();

        //cargamos la vista
        $this->load->view('templates/panel/layout',$data);
	}

    public function add()
    {
        // pasamos los datos básicos del template
        $data['lang'] = "es";
        $data['title'] = $this->proyecto->getNombre() . " | Panel de control";
        $data['view'] = strtolower(__FUNCTION__ . "_" . $this->nameClass);
        $data['robots'] = 'noindex, nofollow';
        $data['project'] = $this->proyecto;
        $data['reference'] = strtoupper(__FUNCTION__ . "-" . $this->nameClass);
        //ruta para los botones y acciones
        $data['path'] = $this->uri->segment(1);
        //pasamos css para esta página
        $data['css'] = $this->load->view('css_module/css','',TRUE);
        //pasamos js para esta página
        $data['js'] = $this->load->view('js_module/js','',TRUE);

        //icono del módulo
        $data['icono'] = $this->icono;
        // titulo del módulo
        $data['h1'] = 'Crear ' . substr(str_replace('_', ' ', $this->nameClass), 0, -1);
        //lista migas pan
        $data['breadcrumb'] = array(str_replace('_', ' ', $this->nameClass), 'Crear ' . substr(str_replace('_', ' ', $this->nameClass), 0, -1));

        //almacenamos todos los roles
        $data['roles'] = $this->doctrine->em->getRepository("Entities\\Roles")->findAll();

        //comprobamos formulario submit
        if (isset($_POST['submit'])){
            //validamos los datos
            $this->form_validation->set_rules('title', 'Título', 'required');
            $this->form_validation->set_rules('text', 'Texto', 'required');

            $this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert">', '</div>');

            if ($this->form_validation->run()) {

                //instanciamos la entidad
                $template = new Entities\Templates;
                //seteamos los datos
                $template->setTitle($this->input->post('title'));
                $template->setText($this->input->post('text'));
                //guardamos
                $this->doctrine->em->persist($template);
                $this->doctrine->em->flush();
                //redireccionamos al edit
                redirect(site_url($data['path'] . '/edit/' . $template->getId()));
            }
        }

        //cargamos la vista
        $this->load->view('templates/panel/layout', $data);

    }

    public function edit($id)
    {
        //pasamos los datos básicos del template
        $data['lang'] = "es";
        $data['title'] = $this->proyecto->getNombre() . " | Panel de control";
        $data['view'] = strtolower(__FUNCTION__ . "_" . $this->nameClass);
        $data['robots'] = 'noindex, nofollow';
        $data['project'] = $this->proyecto;
        $data['reference'] = strtoupper(__FUNCTION__ . "-" . $this->nameClass);
        //ruta para los botones y acciones
        $data['path'] = $this->uri->segment(1);
        //pasamos css para esta página
        $data['css'] = $this->load->view('css_module/css','',TRUE);
        //pasamos js para esta página
        $data['js'] = $this->load->view('js_module/js','',TRUE);

        $data['id'] = $id;
        //obtenemos la plantilla
        $data['getRow'] = $this->doctrine->em->getRepository("Entities\\Templates")->findOneBy(["id" => $id]);
        //icono del módulo
        $data['icono'] = $this->icono;
        // titulo del módulo
        $data['h1'] = 'Editar ' . substr(str_replace('_', ' ', $this->nameClass), 0, -1);
        //lista migas pan
        $data['breadcrumb'] = array(str_replace('_', ' ', $this->nameClass), 'Crear ' . substr(str_replace('_', ' ', $this->nameClass), 0, -1));
        //colección de documentos
        $data['getAttachments'] = $this->doctrine->em->getRepository("Entities\\Attachments")->findBy(["tablerow" => 'template']);
        //comprobamos formulario submit
        if (isset($_POST['submit'])) {
            //validamos los datos
            $this->form_validation->set_rules('nombre', 'Nombre', 'required');
            $this->form_validation->set_rules('apellidos', 'Apellidos', 'required');
            $this->form_validation->set_rules('email', 'Email', 'valid_email');
            $this->form_validation->set_rules('rol', 'Rol', 'required');
            $this->form_validation->set_rules('color','Color','required');

            //solo si pass no es vacío
            if ($this->input->post('pass') != "") {
                $this->form_validation->set_rules('pass', 'Pass');
            }

            $this->form_validation->set_error_delimiters('<div class="alert alert-danger" role="alert">', '</div>');

            if ($this->form_validation->run()) {

                //obtenemos el ROL
                $rol = $this->doctrine->em->find("Entities\\Roles", $this->input->post('rol'));
                //seteamos los datos
                $data['getRow']->setNombre($this->input->post('nombre'));
                $data['getRow']->setApellidos($this->input->post('apellidos'));
                $data['getRow']->setEmail($this->input->post('email'));
                $data['getRow']->setColor($this->input->post('color'));

                //actualizamos sólo si fecha es distinto de vacío
                if ($this->input->post('fnacimiento') != "") {
                    $data['getRow']->setFnacimiento(new \DateTime(formatDateDoct($this->input->post('fnacimiento'))));
                }
                $data['getRow']->setIdrol($rol);
                //si img
                if ($_FILES['image']['name'] != "") {
                    $upload_image = upload('image', 'pages/media/users', 200, 200, '200');

                    if ($upload_image['upload']) {
                        $data['getRow']->setImg($upload_image['res']);
                    }

                }
                //si pass
                if ($this->input->post('pass') != "") {
                    $data['getRow']->setPass(encode_string($this->input->post('pass')));
                }

                //actualizamos
                $this->doctrine->em->flush();


            }
        }

        //cargamos la vista
        $this->load->view('templates/panel/layout', $data);
    }

    public function delete($id)
    {
        //obtenemos el dato mediante id
        $getRow = $this->doctrine->em->getRepository("Entities\\Templates")->findOneBy(["id" => $id]);
        //eliminamos el item
        $this->doctrine->em->remove($getRow);
        $this->doctrine->em->flush();
        //ruta para los botones y acciones
        $path = $this->uri->segment(1);
        //redireccionamos
        redirect(site_url($path));
    }
    /**
     * Metodo que guarda y actualiza los módulos 
     * a los que tiene acceso el usuario.
     */
    public function uploadFile($id)
    {
        //comprobamos si se envio el formulario
        if(isset($_POST['submit-file']))
        {
            //convertimos el vector en un string separado por coma
            $permissions = implode(',',$_POST['module']);
            //instanciamos el rol
            $rol = $this->doctrine->em->find("Entities\\Roles", $id);
            $rol->setPermisos($permissions);
            $this->doctrine->em->flush();
 
        }
        //redireccionamos al edit
        redirect(site_url('configuracion/roles/edit/'.$id));
    }

}
