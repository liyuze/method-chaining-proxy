# Changelog
## 1.1.0 - 2022-05-06

- 新增 `SwitchChainingProxy` ,根据 `caseChaining` 传入的判定值来判断是否真正执行后续的代理方法。
  - 新增 `caseChaining`。
- 修改 `ControlChainingFactory`。
  - 新增 `switch` 方法快速创建对应代理器。
- 修改 `HasMethodChaining` trait。
  - 新增 `switchChaining` 方法，支持在方法链代理器内快速创建 `SwitchChainingProxy` 代理器。

## 1.0.0 - 2022-05-04

- 新增 `MethodChainingProxy` 。
  - 支持代理模式切换。
  - 支持代理模式单次切换。
  - 支持代理值的属性与方法值摘取。
  - 支持后置处理。
- 新增 `MethodChainingFactory` 。
  - 支持快速创建不同代理模式下的代理器。
- 新增 `IfChainingProxy` 根据传入的判定值来判断是否真正执行后续的代理方法。 
  - 新增 `elseChaining` 方法，支持快速进行取非逻辑运算。
- 新增 `ControlChainingFactory` 。
  - 新增 `if` 方法快速创建对应代理器。
  - 新增 `unless` 方法快速创建对应代理器。
- 新增 `HasMethodChaining` trait。
  - 新增 `ifChaining` 和 `unlessChaining` 方法，支持在方法链代理器内快速创建 `IfChainingProxy` 代理器。
