<?php

namespace Tests\Feature;

use App\Http\Services\Master\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Master;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MasterRegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // #[Test]
    // public function it_registers_a_master_successfully()
    // {
    //     // 1️⃣ Мокаємо сервіси
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(true);
    //     });

    //     $masterService = $this->mock(MasterService::class, function ($mock) {
    //         $mock->shouldReceive('createOrUpdate')->once()->andReturn(new Master([
    //             'id' => 1,
    //             'name' => 'Test Master',
    //             'description' => 'Master description',
    //             'address' => 'Test Address',
    //             'latitude' => 50.45,
    //             'longitude' => 30.52,
    //             'place_id' => 'test_place',
    //             'service_id' => 1,
    //         ]));
    //     });

    //     $userService = $this->mock(UserService::class, function ($mock) {
    //         $mock->shouldReceive('createOrUpdateFromMaster')->once()->andReturn(new User([
    //             'id' => 1,
    //             'phone' => '+380501234567',
    //         ]));
    //     });

    //     // 2️⃣ Готуємо тестові дані
    //     $data = [
    //         'phone' => '+380501234567',
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 50.45,
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')), // Фейкове фото
    //         'service_id' => 1,
    //     ];

    //     // 3️⃣ Виконуємо запит
    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     // 4️⃣ Перевіряємо відповідь
    //     $response->assertStatus(200)
    //              ->assertJsonStructure([
    //                  'master' => ['id', 'name', 'description', 'address', 'latitude', 'longitude', 'place_id', 'service_id'],
    //                  'user' => ['id', 'phone'],
    //                  'token',
    //              ]);
    // }

    // #[Test]
    // public function it_fails_if_sms_code_is_invalid()
    // {
    //     // Мокаємо SmsService з помилкою
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(false);
    //     });

    //     $data = [
    //         'phone' => '+380501234567',
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 50.45,
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')),
    //         'service_id' => 1,
    //     ];

    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     $response->assertStatus(400)
    //              ->assertJson(['error' => 'Wrong code']);
    // }

    // #[Test]
    // public function it_fails_if_required_field_is_missing()
    // {
    //     // 1️⃣ Мокаємо сервіси
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(true);
    //     });

    //     // 2️⃣ Пропускаємо обов'язкове поле 'phone'
    //     $data = [
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 50.45,
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')),
    //         'service_id' => 1,
    //     ];

    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     $response->assertStatus(422) // Validation error
    //              ->assertJsonValidationErrors(['phone']);
    // }

    // #[Test]
    // public function it_fails_if_phone_is_invalid_format()
    // {
    //     // Мокаємо SmsService для правильного перевірення коду
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(true);
    //     });

    //     // 2️⃣ Некоректний формат телефону
    //     $data = [
    //         'phone' => 'invalid-phone', // Некоректний номер телефону
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 50.45,
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')),
    //         'service_id' => 1,
    //     ];

    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     $response->assertStatus(422) // Validation error
    //              ->assertJsonValidationErrors(['phone']);
    // }

    // #[Test]
    // public function it_fails_if_latitude_is_invalid()
    // {
    //     // Мокаємо SmsService для правильного перевірення коду
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(true);
    //     });

    //     // 2️⃣ Некоректна широта
    //     $data = [
    //         'phone' => '+380501234567',
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 'invalid-latitude', // Некоректна широта
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')),
    //         'service_id' => 1,
    //     ];

    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     $response->assertStatus(422) // Validation error
    //              ->assertJsonValidationErrors(['latitude']);
    // }

    // #[Test]
    // public function it_fails_if_service_id_is_invalid()
    // {
    //     // Мокаємо SmsService для правильного перевірення коду
    //     $smsService = $this->mock(SmsService::class, function ($mock) {
    //         $mock->shouldReceive('verifyCode')->once()->andReturn(true);
    //     });

    //     // 2️⃣ Некоректний service_id
    //     $data = [
    //         'phone' => '+380501234567',
    //         'name' => 'Test Master',
    //         'description' => 'Master description',
    //         'address' => 'Test Address',
    //         'latitude' => 50.45,
    //         'longitude' => 30.52,
    //         'place_id' => 'test_place',
    //         'sms_code' => 123456,
    //         'photo' => base64_encode(file_get_contents(__DIR__.'/test-image.jpg')),
    //         'service_id' => 'invalid', // Некоректний ID послуги
    //     ];

    //     $response = $this->postJson('/api/auth/master-register', $data);

    //     $response->assertStatus(422) // Validation error
    //              ->assertJsonValidationErrors(['service_id']);
    // }
}
