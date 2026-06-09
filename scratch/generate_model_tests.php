<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = [
    'AdminActivityLog', 'Admin', 'AppSetting', 'ChatHistory',
    'Destination', 'DestinationGallery', 'Event', 'Facility',
    'Permission', 'RecommendationLog', 'Report', 'Review', 'Role', 'User'
];

foreach ($models as $model) {
    $class = "\\App\\Models\\$model";
    $testFile = __DIR__ . '/../tests/Unit/' . $model . 'Test.php';
    
    $content = <<<PHP
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\\$model;

class {$model}Test extends TestCase
{
    public function test_can_instantiate_{$model}()
    {
        \$model = new {$model}();
        \$this->assertInstanceOf({$model}::class, \$model);
    }

    public function test_{$model}_uses_mongodb_connection()
    {
        \$model = new {$model}();
        \$this->assertEquals('mongodb', \$model->getConnectionName());
    }
}
PHP;
    file_put_contents($testFile, $content);
    echo "Generated $testFile\n";
}

// MongoDB Models
$mongoModels = [
    'CarouselBanner', 'ChatSession', 'MongoBeritaPromosi', 'MongoBudaya',
    'MongoDestination', 'MongoEvent', 'MongoFasilitasUmum', 'MongoRecommendation',
    'MongoReport', 'MongoReview'
];

if (!is_dir(__DIR__ . '/../tests/Unit/MongoDB')) {
    mkdir(__DIR__ . '/../tests/Unit/MongoDB', 0755, true);
}

foreach ($mongoModels as $model) {
    $class = "\\App\\Models\\MongoDB\\$model";
    $testFile = __DIR__ . '/../tests/Unit/MongoDB/' . $model . 'Test.php';
    
    $content = <<<PHP
<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\\$model;

class {$model}Test extends TestCase
{
    public function test_can_instantiate_{$model}()
    {
        \$model = new {$model}();
        \$this->assertInstanceOf({$model}::class, \$model);
    }

    public function test_{$model}_uses_mongodb_connection()
    {
        \$model = new {$model}();
        \$this->assertEquals('mongodb', \$model->getConnectionName());
    }
}
PHP;
    file_put_contents($testFile, $content);
    echo "Generated $testFile\n";
}

echo "All model tests generated successfully.\n";
