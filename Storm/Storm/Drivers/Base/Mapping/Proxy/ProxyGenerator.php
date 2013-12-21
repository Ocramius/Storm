<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

abstract class ProxyGenerator implements IProxyGenerator {
    protected $ProxyNamespace;
    protected $ProxyCachePath;

    public function __construct($ProxyNamespace, $ProxyCachePath) {
        $this->ProxyNamespace = $ProxyNamespace;
        if($ProxyCachePath[strlen($ProxyCachePath) - 1] !== DIRECTORY_SEPARATOR) {
            $ProxyCachePath .= DIRECTORY_SEPARATOR;
        }
        $this->ProxyCachePath = $ProxyCachePath;
    }

    final protected function GenerateProxyClassName($EntityType) {
        return str_replace('\\', '_', $EntityType) . '__Proxy';
    }
    
    final protected function GetProxyFullName($ProxyClassName) {
        return $this->ProxyNamespace . '\\' . $ProxyClassName;
    }

    final protected function GenerateProxyFileName($ProxyClassName) {
        return $this->ProxyCachePath . $ProxyClassName . '.php';
    }
}

?>