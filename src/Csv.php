<?php
/**
 * Created by PhpStorm.
 * User: msrana
 * Date: 7/31/18
 * Time: 11:32 PM
 */

namespace Rana\Combo;


class Csv
{
    /*
     * @var $_data for load csv data
     * */
    protected $_data = [];

    /*
     * @filePath to hold the file path of csv file
     * */
    protected $filePath;

    /*
     * @resource to hold the file resource
     * */
    protected $resource = null;

    /**
     * Csv constructor.
     * @param string $fileName
     */
    public function __construct($fileName = "")
    {
        $this->filePath = storage_path("raw_data/probable.csv");
        if (!blank($this->filePath)){
            $this->readCsvFile();
        }
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param null $filePath
     * @return Csv|\RuntimeException
     */
    public function readCsvFile($filePath = null)
    {
        if (!blank($filePath)){
            $this->filePath = $filePath;
        }

        if (blank($this->filePath)){
            return new \RuntimeException("File path dose not exist set a file path");
        }

        $this->resource = fopen($this->getFilePath(),'r');
        return $this;
    }

    /**
     * @return array
     */
    public function getCsvData()
    {
        $this->loadCsvData();
        return $this->_data;
    }

    /**
     * @return $this|\RuntimeException
     */
    public function loadCsvData()
    {
        if (blank($this->resource)){
            $this->readCsvFile();
        }

        if (blank($this->resource)){
            return new \RuntimeException("Resource dose not exists to load csv");
        }

        $title = $this->getTitleRow();

        $this->eachRow(function ($data) use ($title){
           $dataArr = [];
           foreach ($title as $key => $name){
               $dataArr[$name] = $data[$key];
           }
           $this->_data[] = $dataArr;
        });

        return $this;
    }

    /**
     * @return array|false|null
     */
    public function getTitleRow()
    {
        while ($row = fgetcsv($this->resource)){
            return $row;
        }
    }

    /**
     * @param callable $fn
     */
    public function eachRow(callable $fn){
        while ($row = fgetcsv($this->resource)){
            call_user_func_array($fn,[$row]);
        }
    }
}