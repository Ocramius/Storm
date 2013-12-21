<?php

namespace Storm\Drivers\Base\Mapping\Proxy;

class Null__Proxy implements IEntityProxy {
    use \Storm\Core\Helpers\Type;
    use EntityProxyFunctionality;
    
    
    public function __construct() {
        return call_user_func_array([$this, '__ConstructProxy'], func_get_args());
    }
}

class DevelopmentProxyGenerator extends ProxyGenerator {
    const ProxyTemplate = <<<'NOW'
<?php

/**
 * Proxy Class for <EntityClass> auto-generated by STORM.
 *                  --DO NOT MODIFY--
 */

namespace <Namespace>;

use <ProxyInterface> as IProxy;
use <ProxyFunctionality> as ProxyFunctionality;

class <ProxyName> extends <EntityClass> implements IProxy {
    use ProxyFunctionality;
    
    public function __construct() {
        return call_user_func_array([$this, '__ConstructProxy'], func_get_args());
    }

    <OveriddenMethods>
}

?>
NOW;
    
    const OverriddenMethodTemplate = <<<'NOW'
   
    <Modifiers> <Name> (<Parameters>) {
        $this->__Load();
        return parent::<Name>(<ParameterVariables>);
    }
NOW;
    private static $NullProxyReflection;
    private static $NullProxyProperties = array();
    private static $NullProxyMethods = array();

    public function __construct($ProxyNamespace, $ProxyCachePath) {
        if(!isset(self::$NullProxyReflection)) {
            self::$NullProxyReflection = new \ReflectionClass(Null__Proxy::GetType());
            foreach(self::$NullProxyReflection->getProperties() as $Property) {
                self::$NullProxyProperties[$Property->getName()] = $Property;
            }
            foreach(self::$NullProxyReflection->getMethods() as $Method) {
                self::$NullProxyMethods[$Method->getName()] = $Method;
            }
        }

        parent::__construct($ProxyNamespace, $ProxyCachePath);
    }

    public function GenerateProxy($EntityType, callable $EntityLoaderFunction) {
        $EntityReflection = new \ReflectionClass($EntityType);
        $ProxyClassName = $this->GenerateProxyClassName($EntityReflection->getName());
        $FullProxyName = $this->GetProxyFullName($ProxyClassName);
        
        if(class_exists($FullProxyName, false)) {
            return new $FullProxyName($EntityLoaderFunction);
        }
        else {
            $ProxyFileName = $this->GenerateProxyFileName($ProxyClassName);
            
            $this->GenerateProxyClassFile($ProxyFileName, $ProxyClassName, $EntityReflection);
            
            require $ProxyFileName;
            return new $FullProxyName($EntityLoaderFunction);
        }
    }
    
    private function GenerateProxyClassFile($ProxyFileName, $ProxyClassName, \ReflectionClass $EntityReflection) {
        $ProxyClassTemplate = $this->GenerateProxyFileTemplate($ProxyClassName, $EntityReflection);
        $this->GenerateProxyFile($ProxyFileName, $ProxyClassTemplate);
    }

    private function GenerateProxyFile($ProxyFileName, $Template) {
        $DirectoryPath = pathinfo($ProxyFileName, PATHINFO_DIRNAME);
        if (!file_exists($DirectoryPath)) {
            mkdir($DirectoryPath, 0777, true);
        }
        file_put_contents($ProxyFileName, $Template);
    }

    private function GenerateProxyFileTemplate($ProxyClassName, \ReflectionClass $EntityReflection) {
        $ProxyTemplate = self::ProxyTemplate;

        $EntityClass = '\\' . $EntityReflection->getName();

        $OverridenMethods = array();
        foreach($EntityReflection->getMethods() as $Method) {
            if(isset(self::$NullProxyMethods[$Method->getName()])
                    || !$Method->isPublic()
                    || $Method->isStatic()
                    || $Method->isFinal())
                continue;
            else {
                $OverridenMethods[] = $this->GenerateOverridingMethodTemplate($Method);
            }
        }
        $OverridenMethods = implode(PHP_EOL, $OverridenMethods);

        $ProxyTemplate = str_replace('<ProxyInterface>', IEntityProxy::IEntityProxyType, $ProxyTemplate);
        $ProxyTemplate = str_replace('<ProxyFunctionality>', __NAMESPACE__ . '\\EntityProxyFunctionality', $ProxyTemplate);
        $ProxyTemplate = str_replace('<Namespace>', $this->ProxyNamespace, $ProxyTemplate);
        $ProxyTemplate = str_replace('<ProxyName>', $ProxyClassName, $ProxyTemplate);
        $ProxyTemplate = str_replace('<EntityClass>', $EntityClass, $ProxyTemplate);
        $ProxyTemplate = str_replace('<OveriddenMethods>', $OverridenMethods, $ProxyTemplate);

        return $ProxyTemplate;
    }

    private function GenerateOverridingMethodTemplate(\ReflectionMethod $EntityMethod) {
        $MethodTemplate = self::OverriddenMethodTemplate;

        $Modifiers = \Reflection::getModifierNames($EntityMethod->getModifiers());
        $Modifiers[] = 'function';
        if($EntityMethod->returnsReference())
            $Modifiers[] = '&';
        $Modifiers = implode(' ', $Modifiers);

        $Name = $EntityMethod->getName();

        $Parameters = array();
        $ParameterVariables = array();
        foreach($EntityMethod->getParameters() as $Parameter) {
            $ParameterVariables[] = '$' . $Parameter->getName();
            $Parameters[] = $this->GenerateMethodParameter($Parameter);
        }
        $Parameters = implode(', ', $Parameters);
        $ParameterVariables = implode(', ', $ParameterVariables);

        $MethodTemplate = str_replace('<Modifiers>', $Modifiers, $MethodTemplate);
        $MethodTemplate = str_replace('<Name>', $Name, $MethodTemplate);
        $MethodTemplate = str_replace('<Parameters>', $Parameters, $MethodTemplate);
        $MethodTemplate = str_replace('<ParameterVariables>', $ParameterVariables, $MethodTemplate);

        return $MethodTemplate;
    }

    private function GenerateMethodParameter(\ReflectionParameter $MethodParameter) {
        $TypeHint = '';
        if($MethodParameter->isArray())
            $TypeHint = 'array';
        else if($MethodParameter->isCallable())
            $TypeHint = 'callable';
        else {
            if($MethodParameter->getClass() !== null)
                $TypeHint = '\\' . $MethodParameter->getClass()->getName();
        }
        $Reference = $MethodParameter->isPassedByReference() ? '&' : '';
        $VariableName = '$' . $MethodParameter->getName();
        $DefaultValue = '';
        if($MethodParameter->isDefaultValueAvailable()) {
            $DefaultValue .= '= '; 
            if($MethodParameter->isDefaultValueConstant())
                $DefaultValue .= '\\' . $MethodParameter->getDefaultValueConstantName();
            else
                $DefaultValue .= var_export($MethodParameter->getDefaultValue(), true);
        }

        return implode(' ', array_filter([$TypeHint, $Reference, $VariableName, $DefaultValue]));
    }
}

?>