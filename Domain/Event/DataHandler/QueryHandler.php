<?php

/**
 * Selection of needed news entries.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Domain\Event\DataHandler;

class QueryHandler
{

    /**
     * An Instance of lw_db is needed.
     * @param object $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get the informations of an entry with specific id.
     * 
     * @param int $id
     * @return array
     */
    public function selectEntry($id)
    {
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE id = :id ');
        $this->db->bindParameter("id", "i", $id);
        return $this->db->pselect1();
    }

    /**
     * All news entries for a specific page will be loaded.
     * 
     * @param string $lang
     * @return array
     */
    public function selectAllActualEvents($lang)
    {
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND language = :language AND ( :date <= opt2number OR :date <= opt4number ) ORDER BY opt2number ASC ');
        $this->db->bindParameter("lw_object", "s", "lw_events");
        $this->db->bindParameter("date", "i", date("Ymd"));
        $this->db->bindParameter("language", "s", $lang);
        return $this->db->pselect();
    }

    /**
     * All news entries for a specific page will be loaded.
     * 
     * @param array $data
     * @param int $page
     * @return array
     */
    public function selectAllArchivedEvents($lang, $year)
    {
        $this->db->setStatement('SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND language = :language AND opt3number = :year  AND :date > opt2number AND :date > opt4number ORDER BY opt2number DESC ');
        $this->db->bindParameter("lw_object", "s", "lw_events");
        $this->db->bindParameter("year", "i", $year);
        $this->db->bindParameter("date", "i", date("Ymd"));
        $this->db->bindParameter("language", "s", $lang);
        return $this->db->pselect();
    }

    public function selectFirstElementsForTeaser($amount, $lang)
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object AND language = :language AND ( :date <= opt2number OR :date <= opt4number ) ORDER BY opt2number ASC ");
        $this->db->bindParameter("lw_object", "s", "lw_events");
        $this->db->bindParameter("date", "i", date("Ymd"));
        $this->db->bindParameter("language", "s", $lang);
        return $this->db->pselect(0, $amount);
    }

    /**
     * All unique yeas will be returned
     * 
     * return array
     */
    public function selectAllUniqueYears()
    {
        $this->db->setStatement("SELECT DISTINCT opt3number FROM t:lw_master  WHERE lw_object = :lw_object ORDER BY opt3number DESC ");
        $this->db->bindParameter("lw_object", "s", "lw_events");
        return $this->db->pselect();
    }

}