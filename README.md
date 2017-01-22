# 私人微信机器人 php 版


## 特性

* 功能可灵活自定义
* 直接在 Terminal 下使用 `php artisan xxx` 命令执行
* command 的想像空间非常大，又灵活，玩法可以通过写新的 command 充分自定义
* 文件存储将自动生成在 `storage/vbot` 目录下
* 不同微信号，按不同子目录区分存储资源文件
* 可使用图灵机器人回复，根据微信号区分用户以自动关联上下文语义
* 受益于 laravel 的 artisan 特性，可使用 dump 方法对过程变量进行开发调试输出


## 如：近期拜年应用

* 可使用 `php artisan bainian` 执行全部联系人群发拜年
* 或者实现：点击对话自动发送 `预设的祝福`、联系人回复消息自动发送 `预设的回应` 
* 截图：[启动拜年]("https://github.com/webshiyue/laravel-wechat-robot-personal/blob/master/storage/sample/bainian.png") 、[拜年ing]("https://github.com/webshiyue/laravel-wechat-robot-personal/blob/master/storage/sample/bainian_ing.png")


## 环境

环境 php >= 7.0


# git clone 安装

```
git clone https://github.com/webshiyue/laravel-wechat-robot-personal.git
cd vbot
composer install
php artisan key:generate
```

按 .env.sample 适度自定义 .env 还可有更多灵活空间


## Windows 环境下的经验推荐

1. 使用 [UPUPW](http://www.upupw.net/) 快速构建 [Nginx + php7.0](http://www.upupw.net/Nginx/)
2. 使用 mobaXterm 作为 Terminal


## 当前已定义的任务
 
执行 artisan vbot 提示至 task 时可选用：

* `standby` 启动待命，可按已写好的方式响应消息
* `mass` 联系人群发（随机时间间隔 5-15 秒）
* `test` 测试


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

1. 目前此项目只是简单的 vBot 组件在 Laravel 框架下的实例化，还有很大潜力有待挖掘
2. 欢迎共开脑洞，请提 PR 时附带完整注释以便其它伙伴参与共建……
3. 玩法与功能 Issue 请至 [webshiyue/laravel-wechat-robot-personal](https://github.com/webshiyue/laravel-wechat-robot-personal/issues)
4. 关键组件的 Issue 请至 [Hanson/vbot/issues](https://github.com/HanSon/vbot/issues)
5. 欢迎加入 vBot 作者 [Hanson](https://github.com/HanSon) 的企鹅群：492548647


## 其它

1. 此为通过 cURL 调用网页版接口的个人版微信机器人（手机扫码登录）
2. 如需 php 版的微信公众号SDK组件，可至 [EasyWechatSDK](https://github.com/overtrue/wechat) 或 [Laravel-Wechat拓展包](https://github.com/overtrue/laravel-wechat)


## 感谢

* 感谢 [Hanson](https://github.com/HanSon) 构建了基础组件 [HanSon/vbot](https://github.com/HanSon/vbot)
* 感谢企鹅群 492548647 内所有伙伴的交流碰撞


## 打赏支持

* [给买一颗糖](https://github.com/webshiyue/laravel-wechat-robot-personal/blob/master/storage/sample/donate666.png)
* [给买一包糖](https://github.com/webshiyue/laravel-wechat-robot-personal/blob/master/storage/sample/donate6666.png)
* [土豪任性赏](https://github.com/webshiyue/laravel-wechat-robot-personal/blob/master/storage/sample/donate.png)


## License

Open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
