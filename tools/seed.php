<?php
// Run using WP-CLI: all site copy is saved as registered, editable Gutenberg blocks.
if (!defined('ABSPATH')) exit;
function zb_b($name,$attrs,$html) {return '<!-- wp:'.$name.($attrs?' '.wp_json_encode($attrs,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES):'').' -->'.$html.'<!-- /wp:'.$name.' -->';}
function zb_g($class,$content) {return zb_b('group',['className'=>$class],'<div class="wp-block-group '.$class.'">'.$content.'</div>');}
function zb_p($text,$class='') {return zb_b('paragraph',$class?['className'=>$class]:[], '<p'.($class?' class="'.$class.'"':'').'>'.$text.'</p>');}
function zb_h($text,$level=2,$class='') {return zb_b('heading',array_merge(['level'=>$level],$class?['className'=>$class]:[]),'<h'.$level.' class="wp-block-heading'.($class?' '.$class:'').'">'.$text.'</h'.$level.'>');}
function zb_html($html) {return zb_b('html',[],$html);}
function zb_img($number,$alt) {
    $url=esc_url(get_theme_file_uri('assets/placeholder-'.$number.'.svg'));
    return zb_b('image',['sizeSlug'=>'full','linkDestination'=>'none'], '<figure class="wp-block-image size-full"><img src="'.$url.'" alt="'.esc_attr($alt).'"/></figure>');
}
function zb_a($slug,$text,$class='text-link') {return '<a class="'.$class.'" href="'.esc_url(home_url('/'.$slug.'/')).'">'.$text.'</a>';}
function zb_link($slug,$text) {return zb_html(zb_a($slug,$text.' <span aria-hidden="true">↗</span>'));}
function zb_eye($text) {return zb_p($text,'eyebrow');}
function zb_list($items,$class='') {
    $blocks='';foreach($items as $item)$blocks.=zb_b('list-item',[],'<li>'.$item.'</li>');
    return zb_b('list',$class?['className'=>$class]:[], '<ul class="wp-block-list'.($class?' '.$class:'').'">'.$blocks.'</ul>');
}
function zb_intro($heading,$text) {return zb_g('inner-intro',zb_h($heading).zb_g('intro-copy',zb_p($text)));}
function zb_detail($number,$title,$text) {return zb_g('detail-item',zb_eye($number).zb_h($title,3).zb_p($text));}
function zb_notice($text) {return zb_g('notice',zb_p($text));}
function zb_create($slug,$title,$content,$type='page') {
    $existing=get_page_by_path($slug,OBJECT,$type);
    $data=['post_name'=>$slug,'post_title'=>$title,'post_content'=>$content,'post_status'=>'publish','post_type'=>$type];
    if($existing)$data['ID']=$existing->ID;
    $id=wp_insert_post($data,true);if(is_wp_error($id))throw new Exception($id->get_error_message());return $id;
}
$icon='<svg class="care-icon" viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M24 40C24 28 8 32 8 19C8 8 24 10 24 22C24 10 40 8 40 19C40 32 24 28 24 40Z" stroke="currentColor" stroke-width="1.2"/></svg>';
$care='';
foreach([
 ['01 / ВНИМАНИЕ','Забота<br>круглые сутки','Наблюдаем за самочувствием, помогаем в повседневных делах и учитываем индивидуальные потребности.'],
 ['02 / КОМФОРТ','По-домашнему<br>уютно','Уютные комнаты, питание с учётом рекомендаций и помощь в личной гигиене.'],
 ['03 / ОБЩЕНИЕ','Жизнь, наполненная<br>теплом','Прогулки, настольные игры, книги и общение с людьми, которым есть что рассказать.']
] as $item)$care.=zb_g('care-item',zb_eye($item[0]).zb_h($item[1],3).zb_p($item[2]).zb_html($icon));
$heroPictures='';foreach([['Домашний уют','01 / ПРОСТРАНСТВО'],['Внимание к каждому','02 / ЗАБОТА'],['Время для себя','03 / ЖИЗНЬ']] as $i=>$item){
 $heroPictures.=zb_g('hero-picture',zb_img($i+1,'Графическая заглушка: '.$item[0]).zb_html('<p class="image-caption"><span>'.$item[0].'</span><span>'.$item[1].'</span></p>'));
}
$home=zb_g('hero',zb_g('wrap',
    zb_g('hero-intro',zb_g('hero-aside',zb_eye('ЧАСТНЫЙ ПАНСИОНАТ · КОПЕЙСК').zb_p('Внимание к вашим близким.<br>Спокойствие для всей семьи.').zb_html('<a class="text-link" href="#consultation">Обсудить проживание <span aria-hidden="true">↗</span></a>'))
    .zb_g('hero-title',zb_h('Там, где близкие<br>чувствуют себя<br>дома.',1).zb_p('Пансионат для пожилых людей «Забота».<br>Временное и постоянное проживание рядом с Челябинском.')))
    .zb_g('hero-pictures',$heroPictures)
    .zb_html('<div class="hero-bottom"><span>ТЕПЛО. ВНИМАНИЕ. КАЖДЫЙ ДЕНЬ.</span><span>Знакомство с пансионатом <span aria-hidden="true">↓</span></span></div>')))
    .zb_g('intro-section',zb_eye('БОЛЬШЕ, ЧЕМ УХОД').zb_h('У каждого человека — своя история.<br>У нас — <em>внимание к каждой.</em>').zb_p('Создаём семейную, дружественную атмосферу, в которой пожилые люди получают необходимый уход и чувствуют себя как дома.','intro-small').zb_link('o-pansionate','Познакомиться с «Заботой»'))
    .zb_g('care-section',zb_g('wrap',zb_g('section-header',zb_g('section-title',zb_eye('ТО, ЧТО ДЕЙСТВИТЕЛЬНО ВАЖНО').zb_h('Спокойствие начинается<br>с простых вещей.'))).zb_g('care-grid',$care)))
    .zb_g('home-conditions',zb_g('wrap split-editorial',zb_g('editorial-copy',zb_eye('УСЛОВИЯ ПРОЖИВАНИЯ').zb_h('Привычный уют.<br>Бережный уход.').zb_p('Помогаем сохранить привычный ритм жизни и окружаем вниманием с учётом особенностей каждого человека.').zb_list(['Временное и постоянное проживание','Пятиразовое питание, в том числе диетическое','Помощь и наблюдение круглосуточно'],'feature-list').zb_link('usloviya-prozhivaniya','Как устроена жизнь в пансионате')).zb_g('editorial-image',zb_img(4,'Заглушка фотографии уютной комнаты пансионата'))))
    .zb_html('<div class="ribbon"><p>Тепло <span>—</span> Уют <span>—</span> Забота</p></div>');
$scheduleParts=[
 'morning'=>[['07:00','Подъём и утренний туалет'],['07:30','Измерение показателей здоровья'],['09:00','Завтрак'],['09:30','Приём лекарств'],['10:00','Процедуры, досуг и прогулка']],
 'day'=>[['13:30','Обед'],['14:00','Приём лекарств'],['15:00','Тихий час'],['16:00','Полдник'],['16:30','Досуг и прогулка']],
 'evening'=>[['18:00','Ужин'],['18:30','Приём лекарств и процедуры'],['19:00','Просмотр ТВ и общение'],['20:30','Вечерний туалет и подготовка ко сну']]
];
$scheduleHTML='<div class="schedule-tabs" role="tablist" aria-label="Время дня" data-tablist>';
foreach(['morning'=>'Утро','day'=>'День','evening'=>'Вечер'] as $key=>$label)$scheduleHTML.='<button type="button" role="tab" id="tab-'.$key.'" aria-controls="panel-'.$key.'" aria-selected="'.($key==='morning'?'true':'false').'" class="'.($key==='morning'?'active':'').'" data-schedule-tab="'.$key.'">'.$label.'</button>';
$scheduleHTML.='</div>';
foreach($scheduleParts as $key=>$rows){$scheduleHTML.='<div id="panel-'.$key.'" role="tabpanel" aria-labelledby="tab-'.$key.'" data-schedule="'.$key.'"'.($key!=='morning'?' hidden':'').'>';foreach($rows as $row)$scheduleHTML.='<div class="schedule-row"><time>'.$row[0].'</time><span>'.$row[1].'</span></div>';$scheduleHTML.='</div>';}
$home.=zb_g('schedule-section',zb_g('wrap schedule-grid',zb_g('schedule-copy',zb_eye('РИТМ КАЖДОГО ДНЯ').zb_h('Время для заботы.<br>И для жизни.').zb_p('От утренних процедур до вечернего отдыха — день проходит в спокойном и понятном ритме.','schedule-description').zb_link('rasporyadok-dnya','Весь распорядок дня')).zb_g('schedule-content',zb_html($scheduleHTML))));
$home.=zb_g('home-gallery',zb_g('wrap',zb_g('section-header',zb_g('section-title',zb_eye('НАШЕ ПРОСТРАНСТВО').zb_h('Место для тёплых<br>моментов.')).zb_link('fotogalereya','Вся фотогалерея')).zb_g('gallery-preview',zb_g('gallery-preview-item',zb_img(5,'Заглушка фотографии комнат').zb_html('<p>Комнаты для жизни и отдыха <span>01 / ИНТЕРЬЕРЫ</span></p>')).zb_g('gallery-preview-item',zb_img(6,'Заглушка фотографии прогулок').zb_html('<p>Время на свежем воздухе <span>02 / ПРОГУЛКИ</span></p>')))));
$home.=zb_g('review-section',zb_g('wrap review-layout',zb_g('review-intro',zb_eye('ОТЗЫВЫ О ПАНСИОНАТЕ').zb_h('Самое ценное —<br>ваше доверие.').zb_link('otzyvy','Читать отзывы')).zb_g('review-quote',zb_p('“','quote-mark').zb_p('Елена благодарит сотрудников за терпение, доброту и поддержку её тёти. В пансионате та нашла подруг и охотно возвращается после поездок к родным.','quote-text').zb_p('Елена Сергеевна Симонова','quote-author').zb_p('Краткий пересказ отзыва с оригинального сайта','quote-note'))));
$articles=[
 ['kak-ustroit-rodstvennika','Как устроить родственника в дом престарелых?','С чего начать разговор о переезде и на что обратить внимание при знакомстве с пансионатом.','https://zabota74.ru/blog/ID_8.html',[
 'Когда близкому нужен постоянный присмотр, семья может рассмотреть проживание в пансионате. Обсудите это решение вместе с пожилым человеком: расскажите, как будет устроен день, какой уход он получит и как часто вы сможете видеться.',
 'Перед выбором учреждения познакомьтесь с сотрудниками и осмотрите помещения. Уточните состав услуг, условия проживания и порядок оформления. Для частного пансионата перечень документов лучше запросить непосредственно у администрации.',
 'В «Заботе» можно обсудить временное или постоянное проживание. По телефону сотрудники расскажут об условиях и помогут согласовать знакомство с пансионатом.'
 ]],
 ['dom-internat-kashtak','Дом-интернат для престарелых и инвалидов № 1, п. Каштак','Материал из архива о выборе между государственным учреждением и частным пансионатом.','https://zabota74.ru/blog/ID_7.html',[
 'В архиве сайта рассматривается государственный дом-интернат в посёлке Каштак как один из вариантов проживания пожилых людей в Челябинской области. Автор сравнивает порядок оформления и бытовые условия с частными пансионатами.',
 'При выборе места семья обычно оценивает необходимые документы, доступность ухода, питание, общение и организацию повседневной жизни. Условия приёма и перечень документов необходимо уточнять в выбранном учреждении.',
 'В частном пансионате «Забота» предусмотрены временное и постоянное проживание. Узнать о текущих условиях можно у сотрудников по телефону.'
 ]],
 ['doma-prestarelyh-za-pensiyu','Дома престарелых за пенсию','О стоимости проживания, оформлении и вопросах, которые стоит задать заранее.','https://zabota74.ru/blog/ID_6.html',[
 'Стоимость ухода — один из вопросов, которые семья решает при выборе пансионата. В архивном материале оригинального сайта обсуждаются государственные и частные учреждения, различия в оформлении и оплате.',
 'При сравнении вариантов уточните, какие услуги включены в стоимость: питание, помощь в гигиене, сопровождение на прогулках и повседневный уход. Отдельно обсудите индивидуальные потребности пожилого человека.',
 'В «Заботе» стоимость рассчитывается индивидуально и зависит от самостоятельности подопечного. Текущую цену и условия оплаты сообщат сотрудники пансионата.'
 ]],
 ['dom-prestarelyh-chelyabinsk','Дом престарелых в Челябинске','Почему при выборе важны и повседневный уход, и возможность общаться.','https://zabota74.ru/blog/ID_5.html',[
 'Комфортное проживание складывается из повседневных деталей: удобной комнаты, регулярного питания, помощи в бытовых делах и внимательного отношения персонала. Важно, чтобы уход учитывал особенности пожилого человека.',
 'В пансионате «Забота» помогают одеваться, принимать пищу и соблюдать личную гигиену. Сотрудники наблюдают за самочувствием и контролируют приём назначенных лекарств.',
 'Не менее значимо общение. Совместные прогулки, разговоры, книги, настольные игры и просмотр фильмов помогают наполнить день и поддерживать социальные связи.'
 ]],
 ['gosudarstvennyy-dom-internat','Государственный дом-интернат для престарелых','Архивная публикация о проживании пожилых людей, которым необходим постоянный уход.','https://zabota74.ru/blog/ID_4.html',[
 'Материал оригинального сайта посвящён выбору учреждения для пожилого родственника, когда семье трудно обеспечить постоянный присмотр дома.',
 'На этапе знакомства стоит обсудить потребности человека, состав повседневного ухода и условия проживания. Уточняйте действующий порядок приёма непосредственно в выбранной организации.',
 'Для знакомства с условиями частного пансионата «Забота» свяжитесь с сотрудниками и согласуйте визит.'
 ]],
 ['federalnyy-zakon-442','Федеральный закон о доме престарелых № 442','Архивный материал оригинального сайта о социальном обслуживании.','https://zabota74.ru/blog/ID_2.html',[
 'В архиве оригинального сайта есть публикация, посвящённая Федеральному закону № 442-ФЗ и социальному обслуживанию.',
 'Тема сохранена в новой структуре сайта как архивная справка. Историческая публикация не используется как описание действующих требований. Текущую редакцию закона и порядок оформления необходимо проверять по официальным источникам.',
 'Об условиях проживания в частном пансионате «Забота» расскажут сотрудники организации.'
 ]],
 ['kak-popast-v-dom-prestarelyh','Как попасть в дом престарелых','Знакомство с пансионатом и подготовка к новому этапу жизни.','https://zabota74.ru/blog/ID_1.html',[
 'При выборе пансионата важно познакомиться с местом и понять, как будет устроена жизнь близкого человека. Условия проживания, питание, помощь в повседневных делах и общение с ровесниками складываются в общее ощущение комфорта.',
 'Посетите выбранный пансионат, поговорите с сотрудниками и обсудите свои вопросы. Решение лучше принимать совместно с пожилым родственником.',
 'В «Заботе» можно уточнить условия временного и постоянного размещения и индивидуальную стоимость проживания по телефону.'
 ]]
];
function zb_article_card($article,$index) {
 return zb_g('article-card',zb_html('<a class="article-picture" href="'.esc_url(home_url('/stati/'.$article[0].'/')).'" tabindex="-1" aria-hidden="true"><img src="'.esc_url(get_theme_file_uri('assets/placeholder-'.(($index%6)+1).'.svg')).'" alt="" loading="lazy" width="800" height="900"></a>').zb_eye('ЖУРНАЛ «ЗАБОТЫ» · МАТЕРИАЛ ИЗ АРХИВА').zb_h(zb_a('stati/'.$article[0],esc_html($article[1]),''),3).zb_p($article[2],'excerpt').zb_link('stati/'.$article[0],'Читать материал'));
}
$cards='';foreach([0,3,6] as $i)$cards.=zb_article_card($articles[$i],$i);
$home.=zb_g('articles-section',zb_g('wrap',zb_g('section-header',zb_g('section-title',zb_eye('ПОЛЕЗНОЕ ДЛЯ СЕМЬИ').zb_h('Когда есть вопросы.')).zb_link('stati','Все статьи')).zb_g('article-grid',$cards)));
$homeId=zb_create('glavnaya','Главная',$home);
update_option('show_on_front','page');update_option('page_on_front',$homeId);

$about=zb_g('inner-section wrap',zb_intro('Внимание к человеку.<br>В каждом дне.','«Забота» — частный пансионат для пожилых людей. Наша задача — обеспечить уход с учётом индивидуальных особенностей и потребностей, окружить близких вниманием и помочь им чувствовать себя дома.')
 .zb_g('wide-placeholder',zb_img(2,'Графическая заглушка фотографии пансионата').zb_p('Здесь будет фотография пансионата.','small'))
 .zb_g('detail-grid',zb_detail('01 / НАШ ПОДХОД','Семейная атмосфера','Поддерживаем доброжелательные отношения между сотрудниками и подопечными. Для нас важны домашнее тепло и человеческое общение.').zb_detail('02 / НАША ЗАБОТА','Индивидуальный уход','Помогаем людям с ограниченной самостоятельностью и подвижностью, учитываем их привычки и потребности.').zb_detail('03 / НАШ РИТМ','Вместе каждый день','Организуем досуг, прогулки и общение. Внимание к повседневным мелочам помогает сделать жизнь комфортнее.'))
 .zb_h('Кому мы помогаем',2,'body-heading').zb_p('В оригинальном описании пансионата указана возможность размещения людей после инсульта и инфаркта, с болезнями Альцгеймера, Паркинсона, деменцией и нарушениями памяти. Возможность приёма и необходимый объём ухода обсуждаются индивидуально.').zb_link('usloviya-prozhivaniya','Посмотреть условия проживания'));
zb_create('o-pansionate','О пансионате',$about);
$conditions=zb_g('inner-section wrap',zb_intro('Домашняя обстановка.<br>Помощь рядом.','В пансионате предусмотрены временное и постоянное проживание, уютные комнаты, питание и круглосуточное наблюдение за самочувствием. Услуги оказываются с учётом особенностей подопечных и назначений лечащих врачей.')
 .zb_g('wide-placeholder',zb_img(4,'Заглушка фотографии жилой комнаты'))
 .zb_h('Что входит в проживание',2,'body-heading').zb_list(['Размещение в комнатах с домашней обстановкой.','Пятиразовое питание, в том числе диетическое.','Круглосуточное наблюдение за самочувствием.','Ежедневный контроль давления и температуры.','Помощь в личной гигиене и повседневных делах.','Контроль приёма лекарств и выполнение назначений врача.','Досуг и общение с другими проживающими.','Прогулки на свежем воздухе, при необходимости с сопровождением.'],'service-list')
 .zb_g('price-box',zb_g('price-title',zb_eye('СТОИМОСТЬ ПРОЖИВАНИЯ').zb_h('Подберём уход<br>для вашего близкого.')).zb_g('price-copy',zb_p('Стоимость рассчитывается индивидуально и зависит от степени самостоятельности пожилого человека. Позвоните, чтобы уточнить условия и актуальную цену.').zb_html('<a class="text-link" href="#consultation">Обсудить стоимость <span aria-hidden="true">↗</span></a>')))
 .zb_link('perechen-uslug','Подробный перечень услуг').zb_html('<span aria-hidden="true"> &nbsp; &nbsp; </span>').zb_link('rasporyadok-dnya','Распорядок дня'));
zb_create('usloviya-prozhivaniya','Условия проживания',$conditions);
$galleryHTML='<div class="gallery-filters" aria-label="Фильтр фотогалереи">';foreach(['all'=>'Все фотографии','rooms'=>'Комнаты','common'=>'Общие пространства','life'=>'Жизнь пансионата'] as $key=>$label)$galleryHTML.='<button type="button" data-filter="'.$key.'" aria-pressed="'.($key==='all'?'true':'false').'" class="'.($key==='all'?'active':'').'">'.$label.'</button>';$galleryHTML.='</div><p id="gallery-count" class="gallery-count" aria-live="polite">9 фотографий</p><div class="gallery-grid">';
foreach([['Уютные комнаты','rooms'],['Место для отдыха','rooms'],['Домашняя обстановка','rooms'],['Общая гостиная','common'],['Пространство для встреч','common'],['Время за общим столом','common'],['Прогулки на воздухе','life'],['Досуг и общение','life'],['Забота каждый день','life']] as $i=>$item){$galleryHTML.='<article class="gallery-card" data-category="'.$item[1].'"><button type="button" data-gallery="'.$item[0].'" aria-label="Открыть: '.$item[0].'"><span class="placeholder-wrap"><img src="'.esc_url(get_theme_file_uri('assets/placeholder-'.(($i%6)+1).'.svg')).'" alt="Графическая заглушка: '.$item[0].'" loading="lazy" width="800" height="900"></span><span class="gallery-caption"><span>'.$item[0].'</span><span aria-hidden="true">↗</span></span></button></article>';}
$galleryHTML.='</div>';
zb_create('fotogalereya','Фотогалерея',zb_g('inner-section wrap',zb_intro('Здесь начинается<br>ощущение дома.','Комнаты, общие пространства и повседневная жизнь пансионата.').zb_p('В этой демонстрации все фотографии заменены графическими заглушками. Они не изображают реальные помещения или людей.','gallery-disclaimer').zb_html($galleryHTML)));
$reviewCards='';foreach([
 ['Елена Сергеевна Симонова','Елена благодарит сотрудников за терпение и доброе отношение к тёте. Она рассказывает, что та нашла в пансионате подруг.'],
 ['Олеся Васильева','Олеся отмечает условия проживания и отношение персонала. По её словам, бабушке нравится общение и жизнь в пансионате.'],
 ['Владимир','Владимир пишет, что бабушке и дедушке понравились питание и обслуживание. При этом он отмечает небольшую территорию для прогулок.'],
 ['Людмила Алексеевна','Людмила рассказывает о размещении мамы и благодарит сиделок за чуткость. Особенно ценными она считает компанию ровесников и внимание сотрудников.'],
 ['Венцковский Андрей','Андрей оставил краткий положительный отзыв и высоко оценил пансионат.']
] as $r)$reviewCards.=zb_g('review-card',zb_p('“','quote-mark').zb_p($r[1]).zb_p($r[0],'quote-author').zb_p('Краткий пересказ отзыва с zabota74.ru','quote-note'));
zb_create('otzyvy','Отзывы',zb_g('inner-section wrap',zb_intro('Тёплые слова.<br>И честные впечатления.','Истории, которыми поделились близкие наших подопечных на оригинальном сайте пансионата. Сохраняем имена авторов и смысл отзывов.').zb_g('reviews-grid',$reviewCards).zb_html('<p class="source-link"><a class="text-link" href="https://zabota74.ru/gbook/" target="_blank" rel="noopener noreferrer">Прочитать отзывы в оригинале ↗</a></p>')));
$contacts=zb_g('inner-section wrap',zb_g('contacts-grid',zb_g('contacts-details',zb_eye('БУДЕМ РАДЫ ЗНАКОМСТВУ').zb_h('Давайте обсудим<br>вашу ситуацию.').zb_html('<a class="contact-phone" href="tel:+79507274120">+7 950 727 41 20</a><a class="contact-phone" href="tel:+79517712970">+7 951 771 29 70</a>').zb_p('Консультации с 9:00 до 21:00.').zb_g('address-block',zb_eye('АДРЕСА ПАНСИОНАТА').zb_p('г. Копейск, п. Железнодорожный<br>ул. Электровозная, 24').zb_p('г. Копейск<br>ул. Социалистическая, 16').zb_p('Пансионат находится рядом с Челябинском, по Копейскому шоссе.').zb_html('<a class="text-link" target="_blank" rel="noopener noreferrer" href="https://yandex.ru/maps/?text='.rawurlencode('Копейск Социалистическая 16').'">Ул. Социалистическая на карте ↗</a>')))
 .zb_html('<div class="location-panel"><span class="location-label">КОПЕЙСК · СХЕМА РАСПОЛОЖЕНИЯ</span><span class="location-marker" aria-hidden="true"><span>З</span></span><div class="location-inner"><p class="eyebrow">ПАНСИОНАТ «ЗАБОТА»</p><p>п. Железнодорожный<br>ул. Электровозная, 24</p><a class="text-link" target="_blank" rel="noopener noreferrer" href="https://yandex.ru/maps/?text='.rawurlencode('Копейск Железнодорожный Электровозная 24').'">Открыть точный адрес на карте ↗</a></div></div>')));
zb_create('kontakty','Контакты',$contacts);
$table='<table class="schedule-table"><caption class="sr-only">Распорядок дня пансионата Забота</caption><thead><tr><th scope="col">ВРЕМЯ</th><th scope="col">МЕРОПРИЯТИЕ</th></tr></thead><tbody>';
foreach($scheduleParts as $rows)foreach($rows as $row)$table.='<tr><td>'.$row[0].'</td><td>'.$row[1].'</td></tr>';$table.='</tbody></table>';
zb_create('rasporyadok-dnya','Распорядок дня',zb_g('inner-section wrap',zb_intro('Спокойный ритм.<br>Внимание к самочувствию.','Пример распорядка из оригинального описания пансионата. Процедуры и индивидуальные назначения согласуются с лечащим врачом.').zb_html($table)));
zb_create('perechen-uslug','Перечень услуг по уходу',zb_g('inner-section wrap',zb_intro('Помогаем в том,<br>что важно каждый день.','В описание ухода на оригинальном сайте входят бытовая помощь, наблюдение за состоянием и организация повседневной жизни.').zb_list(['Помощь при приёме пищи и кормление.','Контроль своевременного приёма лекарств.','Измерение давления, пульса и температуры.','Помощь в мытье и уходе за кожей.','Смена постельного и нательного белья, переодевание.','Гигиенический уход и смена подгузников.','Уборка и проветривание комнаты.','Наблюдение за положением лежачего подопечного.','Питание с учётом рекомендаций.','Прогулки в сопровождении персонала.','Общая гостиная, книги и настольные игры.','Контроль режима дня и индивидуальное наблюдение.'],'service-list').zb_notice('Объём помощи определяется индивидуально. Процедуры выполняются с учётом медицинских показаний и назначений лечащего врача.').zb_link('usloviya-prozhivaniya','Вернуться к условиям проживания')));
$allCards='';foreach($articles as $i=>$article){
 $content=zb_eye('ЖУРНАЛ «ЗАБОТЫ» · МАТЕРИАЛ ИЗ АРХИВА');
 foreach($article[4] as $paragraph)$content.=zb_p($paragraph);
 $content.=zb_notice('Это краткая редакционная версия архивного материала оригинального сайта. Актуальные условия проживания уточняйте у пансионата.');
 $content.=zb_html('<p class="source-link">Источник: <a href="'.esc_url($article[3]).'" target="_blank" rel="noopener noreferrer">материал на zabota74.ru ↗</a></p>');
 $content.=zb_link('stati','Вернуться ко всем статьям');
 zb_create($article[0],$article[1],$content,'post');
 $allCards.=zb_article_card($article,$i);
}
$news='<div class="news-list">';foreach([
 ['21.12.2017','Поздравляем с Новым годом и Рождеством!','Архивное приглашение провести зимние каникулы в пансионате.','https://zabota74.ru/'],
 ['01.10.2016','День добра и уважения в «Заботе»','Архивная новость о дне благодарности старшему поколению.','https://zabota74.ru/'],
 ['24.08.2016','Архивная акция на проживание','Историческое предложение 2016 года. Цена из архивной акции не является актуальной.','https://zabota74.ru/']
] as $item)$news.='<article><time>'.$item[0].'</time><h3>'.$item[1].'</h3><p>'.$item[2].'</p><a target="_blank" rel="noopener noreferrer" href="'.$item[3].'">Оригинальный сайт ↗</a></article>';$news.='</div>';
zb_create('stati','Статьи',zb_g('inner-section wrap',zb_intro('Когда хочется<br>разобраться.','Материалы оригинального сайта о выборе пансионата и жизни пожилых людей. Публикации представлены в краткой редакционной версии.').zb_g('article-grid archive-grid',$allCards).zb_h('Новости из архива',2,'archive-heading').zb_html($news)));
$sitemap=['o-pansionate'=>'О пансионате','usloviya-prozhivaniya'=>'Условия проживания','fotogalereya'=>'Фотогалерея','stati'=>'Статьи','otzyvy'=>'Отзывы','kontakty'=>'Контакты','rasporyadok-dnya'=>'Распорядок дня','perechen-uslug'=>'Перечень услуг'];
$links=[];foreach($sitemap as $slug=>$label)$links[]=zb_a($slug,$label,'');foreach($articles as $article)$links[]=zb_a('stati/'.$article[0],$article[1],'');
zb_create('karta-sayta','Карта сайта',zb_g('inner-section wrap',zb_list($links,'sitemap-list')));
update_option('blogname','Забота — пансионат для пожилых');
update_option('blogdescription','Тепло. Внимание. Каждый день.');
update_option('blog_public','0');update_option('timezone_string','Asia/Yekaterinburg');
update_option('permalink_structure','/stati/%postname%/');
update_option('default_comment_status','closed');update_option('default_ping_status','closed');
foreach(['hello-world','sample-page','privacy-policy'] as $slug){foreach(['post','page'] as $type){$sample=get_page_by_path($slug,OBJECT,$type);if($sample)wp_delete_post($sample->ID,true);}}
flush_rewrite_rules(false);
WP_CLI::success('Editable site pages and 7 archive articles imported.');
