# Changelog

## 1.0.0 - 2022-05-04

- 新增 `MethodChainingProxy` 
  - 支持代理模式切换
  - 支持代理模式单次切换
  - 支持代理值的属性与方法值摘取
  - 支持后置处理
- 新增 `MethodChainingFactory` 
  - 支持快速创建不同代理模式下的代理器
- 新增 `IfChainingProxy` 根据传入的判定值来判断是否真正执行后续的代理方法。 
  - 支持 `elseChaining`
  - 支持代理模式单次切换
- 新增 `IfChainingFactory` 
  - 支持 `if` 方法链代理器
  - 支持 `unless` 方法链代理器
- 新增 `HasMethodChaining` trait
  - 支持在方法链代理器内快速创建 `ifChaining` 和 `unlessChaining` 代理器
