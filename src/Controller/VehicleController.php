<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Formularios
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Cliente para conectarse a la API
use GuzzleHttp\Client;

// Para obtener la variable de entorno con la URL de la API
use Symfony\Component\Dotenv\Dotenv;

// Se define un controlador para Vehicle
#[Route('/')]
class VehicleController extends AbstractController
{
    // Funcion Index que lista todos los vehiculos que no se hayan vendido
    #[Route('/', name: 'vehicles_list', methods:['get'] )]
    public function vehicles_lists(): Response
    {
        // Se carga la variable de entorno con la URL de la API
        $dotenv = new Dotenv();
        $dotenv->load($this->getParameter('kernel.project_dir').'/.env');

        // Se obtiene la URL de la API
        $api_url = $_ENV['API_URL'];

        // Se crea una instancia del cliente Guzzle
        $client = new Client();

        // Realiza una solicitud GET a la API para obtener el listado de vehiculos
        $response = $client->get($api_url);

        // Obtiene el contenido de la respuesta en formato JSON
        $data = json_decode($response->getBody(), true);

        // Se renderizan dichos datos en la plantilla html
        return $this->render('vehicle/index.html', [
            'vehicles' => $data,
            'controller_name' => 'vehicles_list',
        ]);
    }

    // Funcion que muestra el detalle de un vehiculo
    #[Route('/detail/{id}', name: 'vehicle_detail', methods:['get'] )]
    public function vehicle_detail(int $id): Response
    {
        // Se carga la variable de entorno con la URL de la API
        $dotenv = new Dotenv();
        $dotenv->load($this->getParameter('kernel.project_dir').'/.env');

        // Se obtiene la URL de la API
        $api_url = $_ENV['API_URL'] . '/' . $id;

        // Se crea una instancia del cliente Guzzle
        $client = new Client();

        // Realiza una solicitud GET a la API para obtener el listado de vehiculos
        $response = $client->get($api_url);

        // Obtiene el contenido de la respuesta en formato JSON
        $data = json_decode($response->getBody(), true);

        // Se renderizan dichos datos en la plantilla html
        return $this->render('vehicle/detail.html', [
            'vehicle' => $data,
            'controller_name' => '',
        ]);
    }

    // Funcion que inserta un nuevo vehiculo
    #[Route('/create', name: 'vehicles_create', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Formulario con todos los campos y valores
        $form = $this->createFormBuilder()
            ->add('plate', TextType::class, ['label' => 'Matrícula'])
            ->add('model', TextType::class, ['label' => 'Modelo'])
            ->add('brand', ChoiceType::class, [
                'label' => 'Marca',
                'choices' => [
                    'Honda' => 'Honda',
                    'Mazda' => 'Mazda',
                    'Mitsubishi' => 'Mitsubishi',
                    'Nissan' => 'Nissan',
                    'Subaru' => 'Subaru',
                    'Suzuki' => 'Suzuki',
                    'Toyota' => 'Toyota',
                ],
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'Color',
                'choices' => [
                    'Amarillo' => 'Amarillo',
                    'Azul' => 'Azul',
                    'Blanco' => 'Blanco ',
                    'Gris' => 'Gris',
                    'Marrón' => 'Marrón',
                    'Naranja' => 'Naranja',
                    'Negro' => 'Negro',
                    'Púrpura' => 'Púrpura',
                    'Rojo' => 'Rojo',
                    'Rosa' => 'Rosa',
                    'Verde' => 'Verde',
                ],
            ])
            ->add('image_path', TextType::class, ['label' => 'URL de imagen'])
            ->add('price', NumberType::class, [
                'label' => 'Precio (€)',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Añadir'])
            ->getForm();

        $form->handleRequest($request);

        // Peticion POST
        if ($form->isSubmitted() && $form->isValid()) {

            // Se obtienen los datos del formulario
            $data = $form->getData();

            // Se carga la variable de entorno con la URL de la API
            $dotenv = new Dotenv();
            $dotenv->load($this->getParameter('kernel.project_dir').'/.env');

            // Se obtiene la URL de la API
            $api_url = $_ENV['API_URL'];

            // Se crea una instancia del cliente Guzzle
            $client = new Client();

            // Se realiza la peticion POST a la API con los datos del formulario
            $response = $client->request('POST', $api_url, [
                'form_params' => $data, // Los datos del formulario se envían en la solicitud POST
            ]);

            // Se redirecciona a la vista principal
            return $this->redirectToRoute('vehicles_list', [], Response::HTTP_SEE_OTHER);
        }
 
        // Peticion GET
        return $this->render('vehicle/create.html', [
            'form' => $form->createView(),
            'controller_name' => 'vehicles_create',
        ]);
    }

    // Funcion que inserta un nuevo vehiculo
    #[Route('/plate/{id}', name: 'vehicle_reregister', methods: ['GET', 'POST'])]
    public function vehicle_reregister(Request $request, int $id): Response
    {
        // Formulario con todos los campos y valores
        $form = $this->createFormBuilder()
            ->add('plate', TextType::class, ['label' => 'Nueva matrícula'])
            ->add('submit', SubmitType::class, ['label' => 'Rematricular'])
            ->getForm();

        $form->handleRequest($request);

        // Peticion POST
        if ($form->isSubmitted() && $form->isValid()) {

            // Se obtienen los datos del formulario
            $data = $form->getData();

            // Se carga la variable de entorno con la URL de la API
            $dotenv = new Dotenv();
            $dotenv->load($this->getParameter('kernel.project_dir').'/.env');

            // Se obtiene la URL de la API
            $api_url = $_ENV['API_URL'] . '/plate/' . $id;

            // Se crea una instancia del cliente Guzzle
            $client = new Client();

            // Se realiza la peticion PUT a la API con los datos del formulario
            $response = $client->request('PUT', $api_url, [
                'form_params' => $data, // Los datos del formulario se envían en la solicitud POST
            ]);

            // Se redirecciona a la vista principal
            return $this->redirectToRoute('vehicles_list', [], Response::HTTP_SEE_OTHER);
        }
 
        // Peticion GET
        return $this->render('vehicle/plate.html', [
            'form' => $form->createView(),
            'controller_name' => '',
        ]);
    }

    // Funcion que vende un vehiculo
    #[Route('/sell/{id}', name: 'sell_vehicle', methods:['get'] )]
    public function sell_vehicle(int $id): Response
    {
        // Se carga la variable de entorno con la URL de la API
        $dotenv = new Dotenv();
        $dotenv->load($this->getParameter('kernel.project_dir').'/.env');

        // Se obtiene la URL de la API
        $api_url = $_ENV['API_URL'] . '/sell/' . $id;

        // Se crea una instancia del cliente Guzzle
        $client = new Client();

        // Realiza una solicitud PUT a la API para obtener el listado de vehiculos
        $response = $client->request('PUT', $api_url);

        // Se redirecciona a la vista principal
        return $this->redirectToRoute('vehicles_list', [], Response::HTTP_SEE_OTHER);
    }
    
}