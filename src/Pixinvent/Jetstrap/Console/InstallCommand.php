<?php

namespace Pixinvent\Jetstrap\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'jetstream_materialize:swap {stack : The development stack that should be installed}
                                              {--teams : Indicates if team support should be installed}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Swap TailwindCss for Bootstrap 5.';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    $this->info('Performing swap...');

    // Remove Tailwind Configuration...
    if ((new Filesystem)->exists(base_path('tailwind.config.js'))) {
      (new Filesystem)->delete(base_path('tailwind.config.js'));
    }

    if ((new Filesystem)->exists(base_path('postcss.config.js'))) {
      (new Filesystem)->delete(base_path('postcss.config.js'));
    }

    if ((new Filesystem)->exists(resource_path('views/dashboard.blade.php'))) {
      (new Filesystem)->delete(resource_path('views/dashboard.blade.php'));
    }

    if ((new Filesystem)->exists(resource_path('views/navigation-menu.blade.php'))) {
      (new Filesystem)->delete(resource_path('views/navigation-menu.blade.php'));
    }

    if ((new Filesystem)->exists(resource_path('views/welcome.blade.php'))) {
      (new Filesystem)->delete(resource_path('views/welcome.blade.php'));
    }

    if ((new Filesystem)->exists(resource_path('views/layouts/app.blade.php'))) {
      (new Filesystem)->delete(resource_path('views/layouts/app.blade.php'));
    }

    if ((new Filesystem)->exists(resource_path('views/layouts/guest.blade.php'))) {
      (new Filesystem)->delete(resource_path('views/layouts/guest.blade.php'));
    }

    // "/" Route...
    $this->replaceInFile('/dashboard', '/', base_path('config/fortify.php'));

    // Update routes in web.php
    $originalRoute = <<<'EOD'
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
EOD;

    $newRoute = <<<'EOD'
Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
  Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard');
});
EOD;

    $this->replaceInFile($originalRoute, $newRoute, base_path('routes/web.php'));

    // add components in navbar
    $this->replaceInFile('{{-- <x-switchable-team :team="$team" /> --}}', '<x-switchable-team :team="$team" />', resource_path('views/layouts/sections/navbar/navbar-partial.blade.php'));
    $this->replaceInFile('{{-- <x-banner /> --}}', '<x-banner />', resource_path('views/layouts/contentNavbarLayout.blade.php'));
    $this->replaceInFile('{{-- <x-banner /> --}}', '<x-banner />', resource_path('views/layouts/horizontalLayout.blade.php'));

    // app/views
    (new Filesystem)->deleteDirectory(app_path('View'));
    // Assets...
    $cssFilePath = resource_path('css/app.css');
    file_put_contents($cssFilePath, '');
    (new Filesystem)->ensureDirectoryExists(resource_path('views'));


    // add livewire script file in template
    if (!Str::contains(file_get_contents(resource_path('views/layouts/sections/scripts.blade.php')), "'modals'")) {
      (new Filesystem)->append(resource_path('views/layouts/sections/scripts.blade.php'), $this->livewireScriptDefinition());
    }

    // add livewire style file in template
    if (!Str::contains(file_get_contents(resource_path('views/layouts/sections/styles.blade.php')), "'@livewireStyles'")) {
      (new Filesystem)->append(resource_path('views/layouts/sections/styles.blade.php'), $this->livewireStyleDefinition());
    }

    // Install Stack...
    if ($this->argument('stack') === 'livewire') {

      $this->swapJetstreamLivewireStack();
    }
  }

  /**
   * Swap the Livewire stack into the application.
   *
   * @return void
   */
  protected function swapJetstreamLivewireStack()
  {
    $this->line('');
    $this->info('Installing livewire stack...');

    copy(__DIR__ . '/../../../../stubs/package.json', base_path('package.json'));
    copy(__DIR__ . '/../../../../stubs/vite.config.js', base_path('vite.config.js'));

    // Directories...
    (new Filesystem)->ensureDirectoryExists(resource_path('views/api'));
    (new Filesystem)->ensureDirectoryExists(resource_path('views/auth'));
    (new Filesystem)->ensureDirectoryExists(resource_path('views/profile'));

    // Layouts
    (new Filesystem)->copyDirectory(__DIR__ . '/../../../../stubs/livewire/resources/views/api', resource_path('views/api'));
    (new Filesystem)->copyDirectory(__DIR__ . '/../../../../stubs/livewire/resources/views/profile', resource_path('views/profile'));
    (new Filesystem)->copyDirectory(__DIR__ . '/../../../../stubs/livewire/resources/views/auth', resource_path('views/auth'));

    // Single Blade Views...
    copy(__DIR__ . '/../../../../stubs/livewire/resources/views/terms.blade.php', resource_path('views/terms.blade.php'));
    copy(__DIR__ . '/../../../../stubs/livewire/resources/views/policy.blade.php', resource_path('views/policy.blade.php'));

    // Publish...
    $this->callSilent('vendor:publish', ['--tag' => 'jetstrap-views', '--force' => true]);

    // Teams...
    if ($this->option('teams')) {
      $this->swapJetstreamLivewireTeamStack();
    }

    $this->line('');
    $this->info('Rounding up...');

    $this->line('');
    $this->info('Bootstrap scaffolding swapped for livewire successfully.');
    $this->comment('Please execute the "npm install && npm run build" OR "yarn && yarn build" command to build your assets.');
  }

  /**
   * Swap the Livewire team stack into the application.
   *
   * @return void
   */
  protected function swapJetstreamLivewireTeamStack()
  {
    // Directories...
    (new Filesystem)->ensureDirectoryExists(resource_path('views/teams'));

    (new Filesystem)->copyDirectory(__DIR__ . '/../../../../stubs/livewire/resources/views/teams', resource_path('views/teams'));
  }

  /**
   * Replace a given string within a given file.
   *
   * @param  string  $search
   * @param  string  $replace
   * @param  string  $path
   * @return void
   */
  protected function replaceInFile($search, $replace, $path)
  {
    file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
  }

  /**
   * Get the livewire script definition(s) that should be installed for Livewire.
   *
   * @return string
   */
  protected function livewireScriptDefinition()
  {
    return <<<'EOF'

@stack('modals')
@livewireScripts

EOF;
  }

  /**
   * Get the livewire style definition(s) that should be installed for Livewire.
   *
   * @return string
   */
  protected function livewireStyleDefinition()
  {
    return <<<'EOF'

@livewireStyles

EOF;
  }
}
