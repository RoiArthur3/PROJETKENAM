<?php

namespace App\Providers;

use App\Events\OperationCompleted;
use App\Events\PaymentReceived;
use App\Events\StockMovementCreated;
use App\Events\VehicleAssigned;
use App\Listeners\GenerateInvoiceFromOperation;
use App\Listeners\UpdateInvoicePaymentStatus;
use App\Listeners\UpdateStockLevels;
use App\Listeners\UpdateVehicleAvailability;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        OperationCompleted::class => [
            GenerateInvoiceFromOperation::class,
        ],
        
        VehicleAssigned::class => [
            UpdateVehicleAvailability::class,
        ],
        
        PaymentReceived::class => [
            UpdateInvoicePaymentStatus::class,
        ],
        
        StockMovementCreated::class => [
            UpdateStockLevels::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
