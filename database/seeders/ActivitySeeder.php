<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            [
                'title' => 'Trilho da Preguiça',
                'category' => 'Trilhos',
                'description' => 'Percurso clássico do Gerês com vegetação densa, quedas de água e boas sombras. Ideal para quem quer começar a descobrir a serra sem fazer um trilho demasiado longo.',
                'location' => 'Vilar da Veiga',
                'distance' => 'cerca de 20 min',
                'image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'Cascata do Arado',
                'category' => 'Cascatas',
                'description' => 'Uma das cascatas mais conhecidas da zona, com acesso por estrada de montanha e paisagem muito marcada pela rocha e água.',
                'location' => 'Ermida',
                'distance' => 'cerca de 35 min',
                'image' => 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'Miradouro da Pedra Bela',
                'category' => 'Miradouros',
                'description' => 'Vista ampla sobre a albufeira, montanhas e vales do Gerês. Excelente ao final da tarde.',
                'location' => 'Pedra Bela',
                'distance' => 'cerca de 30 min',
                'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'Praia Fluvial do Alqueirão',
                'category' => 'Praias fluviais',
                'description' => 'Zona tranquila para aproveitar a água nos meses quentes, com bons acessos e ambiente familiar.',
                'location' => 'Caniçada',
                'distance' => 'cerca de 15 min',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Termas do Gerês',
                'category' => 'Termas',
                'description' => 'Espaço histórico associado às águas termais da vila do Gerês, indicado para dias mais calmos.',
                'location' => 'Vila do Gerês',
                'distance' => 'cerca de 25 min',
                'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Espigueiros do Soajo',
                'category' => 'Locais históricos',
                'description' => 'Conjunto histórico de espigueiros em pedra, perfeito para uma visita cultural pela região.',
                'location' => 'Soajo',
                'distance' => 'cerca de 60 min',
                'image' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1200&q=80',
                'is_featured' => false,
            ],
        ])->each(function (array $activity): void {
            Activity::query()->updateOrCreate(
                ['slug' => Str::slug($activity['title'])],
                $activity + [
                    'slug' => Str::slug($activity['title']),
                    'is_active' => true,
                ]
            );
        });
    }
}
