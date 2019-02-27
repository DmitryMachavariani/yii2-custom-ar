<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Yii::$app->user->identity->profile->avatar ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->username ?></p>
                (<?= Yii::$app->user->identity->profile->job ?>)
            </div>
        </div>

        <!-- search form -->
        <form action="/tasks/search" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Поиск..." value="<?=$_GET['q'] ?? ''?>"/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Пользователи', 'icon' => 'user', 'url' => ['user/index']],
                    ['label' => 'Проекты', 'icon' => 'cogs', 'url' => ['projects/index']],
                    ['label' => 'Мои задачи', 'icon' => 'tasks', 'url' => ['tasks/my-tasks']],
                    ['label' => 'Выход', 'icon' => 'user-times', 'url' => ['site/logout']],
                ],
            ]
        ) ?>

    </section>

</aside>
