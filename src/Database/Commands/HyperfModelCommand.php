<?php
/**
 * @author XJ.
 * @Date   2023/7/28 0028
 */

namespace Fatbit\HyperfTools\Database\Commands;

use Hyperf\CodeParser\Project;
use Hyperf\Database\Commands\Ast\ModelRewriteConnectionVisitor;
use Hyperf\Database\Commands\Ast\ModelUpdateVisitor;
use Hyperf\Database\Commands\ModelCommand;
use Hyperf\Database\Commands\ModelData;
use Hyperf\Database\Commands\ModelOption;
use Hyperf\Stringable\Str;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Support\make;

class HyperfModelCommand extends ModelCommand
{
    protected string $modelSuffix = '';


    protected function configure()
    {
        parent::configure();
        $this->addOption('suffix', 'S', InputOption::VALUE_OPTIONAL, 'What name suffix that you want the Model set.');

    }

    public function handle()
    {
        $pool              = $this->input->getOption('pool');
        $this->modelSuffix = $this->getOption('suffix', 'commands.gen:model.suffix', $pool, '');

        parent::handle();
    }

    protected function createModel(string $table, ModelOption $option): void
    {
        $builder = $this->getSchemaBuilder($option->getPool());
        $table   = Str::replaceFirst($option->getPrefix(), '', $table);
        $columns = $this->formatColumns($builder->getColumnTypeListing($table));

        $project = new Project();
        $class   = $option->getTableMapping()[$table] ?? Str::studly(Str::singular($table . '_' . $this->modelSuffix));
        $class   = $project->namespace($option->getPath()) . $class;
        $path    = BASE_PATH . '/' . $project->path($class);

        if (!file_exists($path)) {
            $this->mkdir($path);
            file_put_contents($path, $this->buildClass($table, $class, $option));
        }

        $columns = $this->getColumns($class, $columns, $option->isForceCasts());

        $traverser = new NodeTraverser();
        $traverser->addVisitor(
            make(
                ModelUpdateVisitor::class,
                [
                    'class'   => $class,
                    'columns' => $columns,
                    'option'  => $option,
                ]
            )
        );
        $traverser->addVisitor(make(ModelRewriteConnectionVisitor::class, [$class, $option->getPool()]));
        $data = make(ModelData::class, ['class' => $class, 'columns' => $columns]);
        foreach ($option->getVisitors() as $visitorClass) {
            $traverser->addVisitor(make($visitorClass, [$option, $data]));
        }

        $traverser->addVisitor(new CloningVisitor());

        $originStmts  = $this->astParser->parse(file_get_contents($path));
        $originTokens = $this->lexer->getTokens();
        $newStmts     = $traverser->traverse($originStmts);
        $code         = $this->printer->printFormatPreserving($newStmts, $originStmts, $originTokens);

        file_put_contents($path, $code);
        $this->output->writeln(sprintf('<info>Model %s was created.</info>', $class));

        if ($option->isWithIde()) {
            $this->generateIDE($code, $option, $data);
        }
    }
}