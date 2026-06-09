<?php

namespace Tests\Feature;

use App\Models\BookingRequest;
use App\Models\ContactMessage;
use App\Models\House;
use App\Models\RentalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_responds_and_contains_public_urls(): void
    {
        $this->seed();

        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('content-type'));

        foreach ([
            route('home'),
            route('houses.index'),
            route('houses.show', $house),
            route('houses.units.show', [$house, $unit]),
            route('activities.index'),
            route('contact.index'),
            route('pages.about'),
            route('pages.faq'),
        ] as $url) {
            $this->assertStringContainsString($url, $response->getContent());
        }
    }

    public function test_robots_txt_responds_and_points_to_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $this->assertStringContainsString('text/plain', $response->headers->get('content-type'));
        $response
            ->assertSee('User-agent: *')
            ->assertSee('Allow: /')
            ->assertSee('Sitemap: ' . url('/sitemap.xml'));
    }

    public function test_public_pages_continue_to_return_ok(): void
    {
        $this->seed();

        foreach (['/', '/casas', '/casas/casa-do-rio', '/casas/casa-do-rio/t1-a', '/atividades', '/contactos', '/sobre', '/perguntas-frequentes'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_homepage_and_unit_page_have_basic_meta_tags(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Casas no Gerês para férias em família | Casas Geres</title>', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical" href="' . route('home') . '">', false)
            ->assertSee('<meta property="og:url" content="' . route('home') . '">', false)
            ->assertSee('<meta name="twitter:card"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('LodgingBusiness', false);

        $house = House::query()->where('slug', 'casa-do-rio')->firstOrFail();
        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $this->get('/casas/casa-do-rio/t1-a')
            ->assertOk()
            ->assertSee('<title>T1 A - Casa do Rio no Geres | Casas Geres</title>', false)
            ->assertSee('<meta name="description" content="T1 para ate 2 hospedes.', false)
            ->assertSee('<link rel="canonical" href="' . route('houses.units.show', [$house, $unit]) . '">', false)
            ->assertSee('<meta property="og:image"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Accommodation', false);
    }

    public function test_contact_and_booking_forms_still_work(): void
    {
        $this->seed();

        $this
            ->from('/contactos')
            ->post('/contactos', [
                'name' => 'Cliente SEO',
                'email' => 'seo-contact@example.com',
                'phone' => null,
                'subject' => 'Pedido de informacao',
                'message' => 'Gostava de confirmar disponibilidade.',
            ])
            ->assertRedirect('/contactos')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'seo-contact@example.com',
        ]);

        $unit = RentalUnit::query()->where('slug', 't1-a')->firstOrFail();

        $this
            ->from(route('houses.units.show', [$unit->house, $unit]))
            ->post(route('booking-requests.store'), [
                'rental_unit_id' => $unit->id,
                'name' => 'Cliente Reserva SEO',
                'email' => 'seo-booking@example.com',
                'phone' => null,
                'check_in' => today()->addDays(120)->toDateString(),
                'check_out' => today()->addDays(123)->toDateString(),
                'guests' => 2,
                'message' => 'Pedido de verificacao SEO.',
            ])
            ->assertRedirect(route('houses.units.show', [$unit->house, $unit]))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('booking_status');

        $this->assertDatabaseHas(BookingRequest::class, [
            'email' => 'seo-booking@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_admin_dashboard_remains_accessible_to_authenticated_user(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this
            ->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }
}
