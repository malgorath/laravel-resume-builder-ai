<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade; // Import Blade facade
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use League\CommonMark\CommonMarkConverter; // Import the converter
use League\CommonMark\Exception\CommonMarkException; // Import exception

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination views
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
        
        Blade::directive('markdown', function (string $expression) {
            // $expression will be the variable passed, e.g., '$resume->ai_analysis'
            // We need to handle potential null values and errors gracefully.
            return "<?php
                try {
                    \$markdownInput = {$expression}; // Evaluate the expression passed to the directive
                    if (is_string(\$markdownInput)) {
                        // Configure the converter (optional, add extensions here if needed)
                        \$converter = new \\League\\CommonMark\\CommonMarkConverter([
                            'html_input' => 'escape', // Escape HTML in the source Markdown
                            'allow_unsafe_links' => false, // Disallow javascript: links etc.
                        ]);
                        echo \$converter->convert(\$markdownInput)->getContent();
                    } else {
                        echo ''; // Output empty string if input is not a string (e.g., null)
                    }
                } catch (\\League\\CommonMark\\Exception\\CommonMarkException \$e) {
                    // Log the error or display a user-friendly message
                    // Log::error('Markdown parsing error: ' . \$e->getMessage()); // Example logging
                    echo '<p class=\"text-danger\">Error rendering content.</p>'; // User message
                }
            ?>";
        });
    }
}
