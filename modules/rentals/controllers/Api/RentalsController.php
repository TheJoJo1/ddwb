<?php

declare(strict_types=1);

namespace DDWB\Modules\Rentals\Controllers\Api;

use DDWB\Controller;
use DDWB\Modules\Rentals\Models\Rental;
use DDWB\Request;
use DDWB\Response;

/**
 * API Rentals Controller
 * 
 * Handles rental-related API requests
 */
final class RentalsController extends Controller
{
    private Rental $rentalModel;

    /**
     * Create a new RentalsController instance
     * 
     * @param Rental $rentalModel The rental model
     */
    public function __construct(Rental $rentalModel)
    {
        $this->rentalModel = $rentalModel;
    }

    /**
     * Get all rentals
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function index(Request $request, Response $response): void
    {
        $filters = [
            'status' => $request->getQuery('status'),
            'borrower' => $request->getQuery('borrower'),
        ];

        $rentals = $this->rentalModel->getAllRentals(array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $rentals,
            'count' => count($rentals),
        ]);
    }

    /**
     * Search rentals
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     */
    public function search(Request $request, Response $response): void
    {
        $query = $request->getPost('query', $request->getQuery('query', ''));
        $filters = [
            'status' => $request->getQuery('status'),
        ];

        if (empty($query)) {
            $response->json([
                'success' => false,
                'error' => 'Query parameter is required',
            ], 400);
            return;
        }

        $rentals = $this->rentalModel->search($query, array_filter($filters));

        $response->json([
            'success' => true,
            'data' => $rentals,
            'count' => count($rentals),
        ]);
    }

    /**
     * Get a single rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function show(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->json([
                'success' => false,
                'error' => 'Rental not found',
            ], 404);
            return;
        }

        $response->json([
            'success' => true,
            'data' => $rental,
        ]);
    }

    /**
     * Return a rental
     * 
     * @param Request $request The HTTP request
     * @param Response $response The HTTP response
     * @param array $params Route parameters
     */
    public function returnRental(Request $request, Response $response, array $params): void
    {
        $rentalId = (int)$params['id'];
        $rental = $this->rentalModel->getRentalById($rentalId);

        if ($rental === null) {
            $response->json([
                'success' => false,
                'error' => 'Rental not found',
            ], 404);
            return;
        }

        if ($rental['status'] !== Rental::STATUS_ACTIVE) {
            $response->json([
                'success' => false,
                'error' => 'Rental is not active',
            ], 400);
            return;
        }

        try {
            $this->rentalModel->returnRental($rentalId, [
                'notes' => $request->getPost('notes'),
            ]);

            $response->json([
                'success' => true,
                'message' => 'Rental returned successfully',
                'data' => $this->rentalModel->getRentalById($rentalId),
            ]);
        } catch (\Exception $e) {
            $response->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
