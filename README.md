# 私人微信机器人 php 版

## 特性

* 功能可灵活自定义
* 直接在 Terminal 下使用命令 `php artisan vbot` 执行
* 玩法还可以通过自写 `command` 充分自定义
* 文件存储将自动生成在 `storage/vbot` 目录下
* 不同微信号，按不同子目录区分存储资源文件
* 可使用图灵机器人回复，根据微信号区分用户以自动关联上下文语义
* 受益于 laravel 的 artisan 特性，可使用 dump 方法对过程变量进行开发调试输出

## 环境

1. 环境 php >= 7.0

# git clone 安装

```
git clone https://github.com/webshiyue/laravel-wechat-robot-personal.git
cd vbot
composer install
php artisan key:generate
```

按 .env.sample 适度自定义 .env 还可有更多灵活空间

## windows 推荐
1. windows 可考虑使用 [UPUPW](http://www.upupw.net/) 快速构建 [Nginx + php7.0](http://www.upupw.net/Nginx/)
2. 在 windows 下推荐使用 mobaXterm 作为 Terminal


## 可响应和操作的行为

与组件 [HanSon/vbot](https://github.com/HanSon/vbot/wiki) 同步

- [ ] 消息处理
  - [x] 文字
  - [x] 图片
  - [x] 语音
  - [x] 位置
  - [x] 视频
  - [x] 撤回
  - [x] 表情
  - [x] 红包
  - [x] 转账
  - [x] 名片
  - [x] 好友验证
  - [x] 分享
  - [ ] 小程序
  
- [x] 消息存储
  - [x] 语音
  - [x] 图片
  - [x] 视频
  - [x] 表情

- [x] 消息发送
  - [x] 发送文字
  - [x] 发送图片
  - [x] 发送表情
  - [x] 发送视频

- [ ] 群操作
  - [ ] 创建群
  - [ ] 把某人踢出群
  - [ ] 邀请好友加入群
  - [ ] 修改群名称
  
- [ ] 好友操作
  - [ ] 给好友添加备注
  - [x] 通过好友验证

- [ ] 聊天窗口操作
  - [ ] 置顶聊天会话
  - [ ] 取消聊天会话指定
  
- [ ] 命令行操作信息发送

## 关键组件与基础框架

1. 关键组件 [HanSon/vbot](https://github.com/HanSon/vbot) 作者 [HanSon](https://github.com/HanSon) 组件[Wiki](https://github.com/HanSon/vbot/wiki)
2. 基础框架 [laravel/laravel](https://github.com/laravel/laravel) 当前版本 v5.3

## 共开脑洞与项目共建

1. 玩法与功能 Issue 请至 [webshiyue/laravel-wechat-robot-personal](https://github.com/webshiyue/laravel-wechat-robot-personal/issues)
2. 关键组件的 Issue 请至 [Hanson/vbot/issues](https://github.com/HanSon/vbot/issues)
3. 欢迎共开脑洞，请提 PR 时附带完整注释以便其它伙伴参与共建……
4. 欢迎加入 [Hanson大侠](https://github.com/HanSon) 的 vBot 企鹅群：492548647

## 其它

1. 此为通过 cURL 调用网页版接口的个人版微信机器人（手机扫码登录）
2. 如需 php 版的微信公众号SDK组件，可至 [EasyWechatSDK](https://github.com/overtrue/wechat) 或 [Laravel-Wechat拓展包](https://github.com/overtrue/laravel-wechat)

## License

Open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
