<?php

namespace App\Service;

use App\Entity\Location;
use App\Repository\LocationRepository;

class LocationService
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
    ) {
    }

    /**
     * @return Location[]
     */
    public function getAllLocations(): array
    {
        return $this->locationRepository->findBy([], ['name' => 'ASC']);
    }

    public function getLocationById(int $id): ?Location
    {
        return $this->locationRepository->find($id);
    }

    public function getLocationByName(string $name): ?Location
    {
        return $this->locationRepository->findOneBy(['name' => $name]);
    }

    public function saveLocation(Location $location): void
    {
        $this->locationRepository->save($location);
    }

    public function deleteLocation(Location $location): void
    {
        $this->locationRepository->delete($location);
    }
}
