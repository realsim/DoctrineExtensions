<?php

namespace DoctrineExtensions\Query\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class DistinctOn extends FunctionNode
{
    private $distinctExpression = null;
    private $selectExpression = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->distinctExpression = $parser->PathExpression(PathExpression::TYPE_STATE_FIELD);

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        $this->selectExpression = $parser->SimpleSelectExpression();
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return \sprintf('DISTINCT ON (%s) %s',
            $sqlWalker->walkPathExpression($this->distinctExpression),
            $sqlWalker->walkSimpleSelectExpression($this->selectExpression),
        );
    }
}
