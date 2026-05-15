<?php

use App\Services\KelulusanService;
use App\Repositories\SiswaRepository;

beforeEach(function () {
    $this->service = new KelulusanService();
});

test('it can calculate statistics', function () {
    $statistik = $this->service->statistik();
    
    expect($statistik)->toBeArray();
});

test('it can fetch available years', function () {
    $years = $this->service->availableYears();
    
    expect($years)->toBeArray();
    if (count($years) > 0) {
        expect($years[0])->toBeInt();
    }
});

test('it can validate student data', function () {
    // Accessing private method for testing purpose or testing through public method
    // Let's test through the public update/create method with invalid data
    $result = $this->service->create([
        'nisn' => '123', // Too short
        'nama' => 'Ab'   // Too short
    ]);
    
    expect($result['success'])->toBeFalse();
    expect($result['errors'])->toHaveKey('nisn');
    expect($result['errors'])->toHaveKey('nama');
});
