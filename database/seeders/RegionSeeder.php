<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sacamos las regiones que realmente existen en la tabla sales
        $regions = DB::table('sales')->distinct()->pluck('region');

        foreach ($regions as $regionName) {
            $response = Http::withHeaders([
                'User-Agent' => 'LaravelApp (tucorreo@dominio.com)' // recomendado
            ])->get('https://nominatim.openstreetmap.org/search.php', [
                'q' => $regionName,
                'format' => 'jsonv2',
                'limit' => 1,
            ]);

            Log::info("Buscando coordenadas para: $regionName", ['response' => $response->json()]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                Region::updateOrCreate(
                    ['name' => $regionName],
                    ['lat' => $data['lat'], 'lng' => $data['lon']]
                );
                echo "Guardada: $regionName ({$data['lat']}, {$data['lon']})\n";
            } else {
                echo "No se encontró coordenada para $regionName\n";
            }

            // Pausa para evitar baneo por abuso
            sleep(1);
        }
    }
}
