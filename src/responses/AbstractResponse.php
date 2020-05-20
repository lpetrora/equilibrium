<?php 
namespace equilibrium\responses;

abstract class AbstractResponse implements IResponse{
    abstract function execute();
}
