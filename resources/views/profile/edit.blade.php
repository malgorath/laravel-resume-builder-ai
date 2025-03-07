@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Edit Profile</h2>

    <form method="POST" action="{{ route('profile.update', $user->id) }}">
        @csrf
        @method('POST')

        <div class="mb-4">
            <label class="block text-sm font-medium">Address</label>
            <input type="text" name="address" value="{{ old('address', $user->userDetail->address ?? '') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->userDetail->phone ?? '') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">LinkedIn</label>
            <input type="text" name="linkedin" value="{{ old('linkedin', $user->userDetail->linkedin ?? '') }}" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Details</button>
    </form>

    <h3 class="text-lg font-semibold mt-6">Skills</h3>

    <form method="POST" action="{{ route('profile.skills.add', $user->id) }}" class="mt-2">
        @csrf
        <div class="flex gap-2">
            <input type="text" name="skill" class="border rounded px-3 py-2 w-full" placeholder="New skill">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Add</button>
        </div>
    </form>

    <ul class="mt-4">
        @foreach($user->userSkills as $skill)
            <li class="flex justify-between items-center bg-gray-100 p-2 rounded mt-2">
                {{ $skill->skill }}
                <form method="POST" action="{{ route('profile.skills.delete', $skill->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection
