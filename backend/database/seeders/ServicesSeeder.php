<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Copia B/N',
                'description' => 'Copias en blanco y negro en tamaños A6, A5, A4, A3, el precio es por página',
                'cost' => '0',
                'price' => '0.10',
            ],
            [
                'name' => 'Copia Color',
                'description' => 'Copias a color en tamaños A6, A5, A4, A3, el precio varia segun cantidad de color',
                'cost' => '0',
                'price' => '0.30',
            ],
            [
                'name' => 'Impresión B/N',
                'description' => 'Impresión en blanco y negro en tamaños A6, A5, A4, A3, el precio es por página',
                'cost' => '0',
                'price' => '0.10',
            ],
            [
                'name' => 'Impresión Color',
                'description' => 'Impresión a color en tamaños A6, A5, A4, A3, el precio varia segun cantidad de color',
                'cost' => '0',
                'price' => '0.30',
            ],
            [
                'name' => 'Banner 13oz',
                'description' => 'Impresión en banner de 13 onzas, precio por metro cuadrado',
                'cost' => '0',
                'price' => '16',
            ],
            [
                'name' => 'Banner 10oz',
                'description' => 'Impresión en banner de 10 onzas, precio por metro cuadrado',
                'cost' => '0',
                'price' => '14',
            ],
            [
                'name' => 'Vinil',
                'description' => 'Impresión de vinil a color o b/n, precio por metro cuadrado',
                'cost' => '0',
                'price' => '24',
            ],
            [
                'name' => 'Vinil laminado',
                'description' => 'Impresión de vinil a color o b/n más laminado transparente, precio por metro cuadrado',
                'cost' => '0',
                'price' => '30',
            ],
            [
                'name' => 'Vinil troquelado',
                'description' => 'Impresión de vinil a color o b/n más troquelado, precio por metro cuadrado',
                'cost' => '0',
                'price' => '35',
            ],
            [
                'name' => 'Vinil Celtex',
                'description' => 'Impresión de vinil c/s laminado a color o b/n en celtex de 5mm, precio por metro cuadrado',
                'cost' => '0',
                'price' => '40',
            ],
            [
                'name' => 'Enmicado fotocheck',
                'description' => 'enmicado de fotochecks',
                'cost' => '0',
                'price' => '1',
            ],
            [
                'name' => 'Fotografía profesional',
                'description' => 'sesiones fotograficas para momentos especiales',
                'cost' => '0',
                'price' => '20',
            ],
            [
                'name' => 'Anillado',
                'description' => 'anillado de hojas',
                'cost' => '0',
                'price' => '2',
            ],
            [
                'name' => 'Tarjeta de presentación',
                'description' => 'Diseño e impresión de tarjetas en mdf de 3 mm, precio por millar',
                'cost' => '0',
                'price' => '65',
            ],
            [
                'name' => 'Volantes',
                'description' => 'Impresipon de volantes en tamaños A5, A6 en papel periodico o couche, precio por millar',
                'cost' => '0',
                'price' => '70',
            ],
            [
                'name' => 'Diseño de logo',
                'description' => 'Diseño de logotipo enfocado en tu negocio o marca',
                'cost' => '0',
                'price' => '50',
            ],
            [
                'name' => 'Roll Screen',
                'description' => 'Estructura metalica para roll screen más banner de 1.50m x 1.0m en 13oz',
                'cost' => '0',
                'price' => '140',
            ],
            [
                'name' => 'Talonarios',
                'description' => 'Impresión de talonarios de boletas, rifas etc., costo por millar',
                'cost' => '0',
                'price' => '30',
            ],
        ];


        foreach ($services as $service) {
            $service['profit'] =$service['price'] - $service['cost'];
            
            Service::create($service);
        }
    }
}
