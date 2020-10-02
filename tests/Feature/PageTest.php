<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageTest extends TestCase
{
    /** @test */
    public function testAppPageIsShowing()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function testAnyOtherPagesAreRedirectingToApp()
    {
        $test = $this->get('/test');
        $test->assertStatus(200);

        $page = $this->get('/test/somepage');
        $page->assertStatus(200);
    }
}
