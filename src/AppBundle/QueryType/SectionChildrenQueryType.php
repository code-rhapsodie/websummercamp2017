<?php
namespace AppBundle\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\QueryType\QueryType;
use eZ\Publish\API\Repository\Values\Content\Query;

class SectionChildrenQueryType implements QueryType
{
    /**
     * @param array $parameters
     *          parentLocationId = "@=location.id"
     *          subtree = "@=location.pathString"
     *          type = [<ContentTypeIdentifier1>, ...]
     *          sortClauseDirection = 'ascending' | 'descending'
     *          limit = 10 by default
     * @return Query
     */
    public function getQuery(array $parameters = [])
    {
        if (isset($parameters['visibility'])) {
            $criteria[] = new Query\Criterion\Visibility(($parameters['visibility']?Query\Criterion\Visibility::VISIBLE:Query\Criterion\Visibility::HIDDEN));
        } else {
            $criteria[] = new Query\Criterion\Visibility(Query\Criterion\Visibility::VISIBLE);
        }

        if(isset($parameters['parentLocationId'])) {
            $criteria[] = new Query\Criterion\ParentLocationId($parameters['parentLocationId']);
        }

        if(isset($parameters['subtree'])) {
            $criteria[] = new Query\Criterion\Subtree($parameters['subtree']);
        }

        if (isset($parameters['type'])) {
            $criteria[] = new Query\Criterion\ContentTypeIdentifier($parameters['type']);
        }

        if (isset($parameters['sortClauseDirection'])){
            $sortClauseDirection = $parameters['sortClauseDirection'] == 'ascending' ? Query::SORT_ASC : Query::SORT_DESC;
        }else{
            $sortClauseDirection = Query::SORT_ASC;
        }

        // 10 is the default limit we set, but you can have one defined in the parameters
        return new LocationQuery([
            'filter' => new Query\Criterion\LogicalAnd($criteria),
            'sortClauses' => [new Query\SortClause\DatePublished($sortClauseDirection)],
            'limit' => isset($parameters['limit']) ? $parameters['limit'] : 10,
        ]);
    }

    public static function getName()
    {
        return 'SectionChildren';
    }

    /**
     * Returns an array listing the parameters supported by the QueryType.
     * @return array
     */
    public function getSupportedParameters()
    {
        return ['parentLocationId', 'type', 'limit', 'subtree', 'sortClauseDirection', 'visibility'];
    }
}