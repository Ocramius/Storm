<?php

namespace Storm\Drivers\Base\Relational\Expressions\Operators;

final class Binary {
    //Arthmetic
    const Addition = 0;
    const Subtraction = 1;
    const Multiplication = 2;
    const Division = 3;
    const Modulus = 4;
    const Power = 5;
    
    //Bitwise
    const BitwiseAnd = 100;
    const BitwiseOr = 101;
    const BitwiseXor = 102;
    const ShiftLeft = 103;
    const ShiftRight = 104;
    
    //Logical
    const LogicalAnd = 200;
    const LogicalOr = 201;
    const Equality = 202;
    const NullSafeEquality = 203;
    const Inequality = 204;
    const NullSafeInequality = 205;
    const LessThan = 206;
    const LessThanOrEqualTo = 207;
    const GreaterThan = 208;
    const GreaterThanOrEqualTo = 209;
    
    //String
    const Concatenation = 208;
    
    //Other
    const In = 300;
    const MatchesRegularExpression = 301;
}

?>