<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Models\StockLevel;
use App\Services\StockService;
use App\Events\StockMovementCreated;
use App\Listeners\UpdateStockLevel;
use App\Listeners\CreateInvoiceForStockEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockEventsListenersTest extends TestCase
{
    use RefreshDatabase;

    protected $warehouse;
    protected $product;
    protected $stockLevel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->warehouse = Warehouse::factory()->create();
        $this->product = Product::factory()->create();
        $this->stockLevel = StockLevel::factory()->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'current_stock' => 100,
            'reserved_stock' => 10,
        ]);
    }

    /** @test */
    public function it_updates_stock_level_when_movement_is_created()
    {
        // Create a stock entry movement
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'entree',
            'quantity' => 50,
            'is_validated' => true,
        ]);

        // Trigger the event
        $event = new StockMovementCreated($movement);
        $listener = new UpdateStockLevel();
        $listener->handle($event);

        // Assert stock level was updated
        $this->stockLevel->refresh();
        $this->assertEquals(150, $this->stockLevel->current_stock);
        $this->assertEquals(60, $this->stockLevel->available_stock);
    }

    /** @test */
    public function it_creates_invoice_for_stock_entry()
    {
        // Create a stock entry movement with supplier
        $movement = StockMovement::factory()->create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'type' => 'entree',
            'quantity' => 10,
            'unit_price' => 1000,
            'supplier_id' => 1,
            'is_validated' => true,
        ]);

        // Trigger the event
        $event = new StockMovementCreated($movement);
        $listener = new CreateInvoiceForStockEntry();
        $listener->handle($event);

        // Assert invoice was created
        $this->assertDatabaseHas('invoices', [
            'reference' => 'AUTO-' . $movement->reference,
            'client_id' => $movement->supplier_id,
            'total_ht' => 10000,
            'total_tva' => 1800,
            'total_ttc' => 11800,
        ]);
    }

    /** @test */
    public function stock_service_creates_movement_and_triggers_events()
    {
        $stockService = new StockService();

        // Create entry using service
        $movement = $stockService->createEntry([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 25,
            'unit_price' => 500,
            'reason' => 'Test entry',
        ]);

        // Assert movement was created
        $this->assertInstanceOf(StockMovement::class, $movement);
        $this->assertEquals('entree', $movement->type);
        $this->assertEquals(25, $movement->quantity);

        // Assert stock level was updated automatically
        $this->stockLevel->refresh();
        $this->assertEquals(125, $this->stockLevel->current_stock);
    }

    /** @test */
    public function it_handles_stock_exit_correctly()
    {
        $stockService = new StockService();

        // Create exit using service
        $movement = $stockService->createExit([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 30,
            'reason' => 'Test exit',
        ]);

        // Assert movement was created
        $this->assertInstanceOf(StockMovement::class, $movement);
        $this->assertEquals('sortie', $movement->type);
        $this->assertEquals(30, $movement->quantity);

        // Assert stock level was updated
        $this->stockLevel->refresh();
        $this->assertEquals(70, $this->stockLevel->current_stock);
    }

    /** @test */
    public function it_prevents_negative_stock()
    {
        $stockService = new StockService();

        // Try to exit more than available
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuffisant');

        $stockService->createExit([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100, // More than available (90)
            'reason' => 'Test exit',
        ]);
    }
}
