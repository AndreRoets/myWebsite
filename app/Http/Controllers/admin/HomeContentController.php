<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends Controller
{
    private const FILE = 'home_content.json';

    public static function defaults(): array
    {
        return [
            'hero_title' => 'Discover the Exceptional',
            'hero_subtitle' => 'Exceptional homes. Extraordinary views. Exclusive living',
            'hero_button_text' => 'View Properties',
            'hero_button_url' => '/properties',
            'cat1_title' => 'Waterfront Estates',
            'cat1_text' => 'Exclusive seaside luxury with unmatched serenity.',
            'cat1_url' => '/properties',
            'cat2_title' => 'Urban Penthouses',
            'cat2_text' => 'Experience the skyline from the top — modern & elite.',
            'cat2_url' => '/properties',
            'cat3_title' => 'Exclusive Villas',
            'cat3_text' => 'Private, elegant homes for the most discerning buyers.',
            'cat3_url' => '/properties',
        ];
    }

    public static function load(): array
    {
        $defaults = self::defaults();
        if (Storage::disk('local')->exists(self::FILE)) {
            $data = json_decode(Storage::disk('local')->get(self::FILE), true) ?? [];
            return array_merge($defaults, $data);
        }
        return $defaults;
    }

    public function edit()
    {
        return view('admin.home_content.edit', ['content' => self::load()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:500',
            'hero_button_text' => 'required|string|max:100',
            'hero_button_url' => 'required|string|max:500',
            'cat1_title' => 'required|string|max:255',
            'cat1_text' => 'required|string|max:500',
            'cat1_url' => 'required|string|max:500',
            'cat2_title' => 'required|string|max:255',
            'cat2_text' => 'required|string|max:500',
            'cat2_url' => 'required|string|max:500',
            'cat3_title' => 'required|string|max:255',
            'cat3_text' => 'required|string|max:500',
            'cat3_url' => 'required|string|max:500',
        ]);

        Storage::disk('local')->put(self::FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.home-content.edit')->with('status', 'Home page content updated.');
    }
}
