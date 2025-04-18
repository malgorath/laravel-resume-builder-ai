@extends('layouts.app')

@section('content')
    {{-- Add container, padding, etc. as needed from your app layout --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl font-semibold mb-4">Resume Builder</h2>
                    <div class="flex mb-4">
                        <img src="{{ asset('images/logo-resume-builder.jpg') }}" alt="{{ config('app.name', 'Laravel') }} Logo" class="w-40 mr-4 float-left">
                        <p class="flex-1">
                            Looking started he up perhaps against. How remainder all additions get elsewhere
                            resources. One missed shy wishes supply design answer formed. Prevent on present
                            hastily passage an subject in be. Be happiness arranging so newspaper defective
                            affection ye. Families blessing he in to no daughter.
                        </p>
                    </div>
                    <p class="mb-4">
                        May musical arrival beloved luckily adapted him. Shyness mention married son she
                        his started now. Rose if as past near were. To graceful he elegance oh moderate
                        attended entrance pleasure. Vulgar saw fat sudden edward way played either.
                        Thoughts smallest at or peculiar relation breeding produced an. At depart spirit
                        on stairs. She the either are wisdom praise things she before. Be mother itself
                        vanity favour do me of. Begin sex was power joy after had walls miles.
                    </p>
                    {{-- Add classes to other paragraphs as well --}}

                </div>
            </div>
        </div>
    </div>
@endsection
