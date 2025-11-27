<?php
/**
 * Plugin Name: QU Simple AOS
 * Description: 심플하고 가벼운 AOS(Animate On Scroll) 로더 플러그인. 관리자에서 init 옵션과 JS 소스(CDN/로컬)를 설정할 수 있습니다.
 * Version: 2.0.0
 * Author: QU
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 사용 중인 AOS 라이브러리 버전 (CDN/로컬 공통)
define( 'QU_AOS_LIB_VERSION', '2.3.4' );

/**
 * 옵션 기본값
 */
function qu_aos_get_default_settings() {
    return array(
        'source'         => 'cdn', // 'cdn' 또는 'local'
        'duration'       => '1000',
        'easing'         => '',
        'offset'         => '',
        'delay'          => '',
        'once'           => '',
        'mirror'         => '',
        'disable_mobile' => '',    // 모바일에서 AOS 비활성화 여부
    );
}

/**
 * 옵션 가져오기 (기본값 merge)
 */
function qu_aos_get_settings() {
    $saved    = get_option( 'qu_aos_settings', array() );
    $defaults = qu_aos_get_default_settings();
    return wp_parse_args( $saved, $defaults );
}

/**
 * 스크립트 / 스타일 로드
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin() ) {
        return;
    }

    $settings   = qu_aos_get_settings();
    $plugin_url = plugin_dir_url( __FILE__ );

    // 🔧 0) 모바일 비활성화 옵션 처리
    if ( ! empty( $settings['disable_mobile'] ) && wp_is_mobile() ) {
        return; // 모바일에서는 AOS 완전히 비활성화
    }

    // 1) CSS / JS 소스 선택 (CDN 또는 로컬)
    if ( $settings['source'] === 'local' ) {

        // 로컬 파일 사용
        wp_enqueue_style(
            'qu-aos-css',
            $plugin_url . 'assets/css/aos.css',
            array(),
            QU_AOS_LIB_VERSION
        );

        wp_enqueue_script(
            'qu-aos-js',
            $plugin_url . 'assets/js/aos.js',
            array(),
            QU_AOS_LIB_VERSION,
            true
        );

    } else {

        // CDN 사용 (기본값)
        wp_enqueue_style(
            'qu-aos-css',
            'https://cdn.jsdelivr.net/npm/aos@' . QU_AOS_LIB_VERSION . '/dist/aos.css',
            array(),
            QU_AOS_LIB_VERSION
        );

        wp_enqueue_script(
            'qu-aos-js',
            'https://cdn.jsdelivr.net/npm/aos@' . QU_AOS_LIB_VERSION . '/dist/aos.js',
            array(),
            QU_AOS_LIB_VERSION,
            true
        );
    }

    // 2) init 옵션 구성 (빈 값은 포함 X)
    $init_options = array();

    if ( $settings['duration'] !== '' ) {
        $init_options['duration'] = (int) $settings['duration'];
    }
    if ( $settings['easing'] !== '' ) {
        $init_options['easing'] = sanitize_text_field( $settings['easing'] );
    }
    if ( $settings['offset'] !== '' ) {
        $init_options['offset'] = (int) $settings['offset'];
    }
    if ( $settings['delay'] !== '' ) {
        $init_options['delay'] = (int) $settings['delay'];
    }
    if ( ! empty( $settings['once'] ) ) {
        $init_options['once'] = true;
    }
    if ( ! empty( $settings['mirror'] ) ) {
        $init_options['mirror'] = true;
    }

    $json = ! empty( $init_options ) ? wp_json_encode( $init_options ) : '{}';

    $inline_js = "
    document.addEventListener('DOMContentLoaded', function() {
        if ( typeof AOS !== 'undefined' ) {
            AOS.init({$json});
        }
    });
    ";

    wp_add_inline_script( 'qu-aos-js', $inline_js );
}, 20 );

/**
 * 설정 페이지 등록
 */
add_action( 'admin_menu', function() {
    add_options_page(
        'QU Simple AOS',
        'QU Simple AOS',
        'manage_options',
        'qu-simple-aos',
        'qu_aos_render_settings_page'
    );
});

/**
 * 설정 등록
 */
add_action( 'admin_init', function() {
    register_setting(
        'qu_aos_settings_group',
        'qu_aos_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'qu_aos_sanitize_settings',
            'default'           => qu_aos_get_default_settings(),
        )
    );
});

/**
 * 옵션 값 sanitize
 */
function qu_aos_sanitize_settings( $input ) {
    $output = qu_aos_get_default_settings();

    // source: 'cdn' 또는 'local'
    if ( isset( $input['source'] ) && in_array( $input['source'], array( 'cdn', 'local' ), true ) ) {
        $output['source'] = $input['source'];
    }

    $output['duration'] = isset( $input['duration'] ) && $input['duration'] !== ''
        ? (int) $input['duration']
        : '1000';

    $output['easing']   = isset( $input['easing'] ) ? sanitize_text_field( $input['easing'] ) : '';
    $output['offset']   = isset( $input['offset'] ) ? (int) $input['offset'] : '';
    $output['delay']    = isset( $input['delay'] ) ? (int) $input['delay'] : '';
    $output['once']     = ! empty( $input['once'] ) ? '1' : '';
    $output['mirror']   = ! empty( $input['mirror'] ) ? '1' : '';

    // 모바일 비활성화 옵션
    $output['disable_mobile'] = ! empty( $input['disable_mobile'] ) ? '1' : '';

    return $output;
}

/**
 * 설정 페이지 출력
 */
function qu_aos_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $settings = qu_aos_get_settings();
    ?>
    <div class="wrap">
        <h1>QU Simple AOS 설정</h1>
        <p>이 플러그인은 AOS(Animate On Scroll)를 간단하게 로드하고, 공통 init 옵션과 JS 소스(CDN/로컬)를 설정할 수 있도록 도와줍니다.</p>

        <p><strong>현재 AOS 라이브러리 버전:</strong> <?php echo esc_html( QU_AOS_LIB_VERSION ); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields( 'qu_aos_settings_group' ); ?>
            <table class="form-table" role="presentation">

                <!-- 소스 선택: CDN vs 로컬 -->
                <tr>
                    <th scope="row">AOS 스크립트 소스</th>
                    <td>
                        <label>
                            <input type="radio" name="qu_aos_settings[source]" value="cdn"
                                <?php checked( $settings['source'], 'cdn' ); ?>>
                            CDN (jsDelivr)
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="qu_aos_settings[source]" value="local"
                                <?php checked( $settings['source'], 'local' ); ?>>
                            로컬 파일 (이 플러그인의 <code>assets/js</code>, <code>assets/css</code> 사용)
                        </label>
                        <p class="description">
                            운영 정책이나 서버 환경에 따라 선택하세요. 클라이언트/기업 사이트나 외부 스크립트 정책이 엄격한 경우 로컬 사용을 추천합니다.
                        </p>
                    </td>
                </tr>

                <!-- 🔧 모바일 비활성화 옵션 -->
                <tr>
                    <th scope="row">Disable on Mobile</th>
                    <td>
                        <label>
                            <input type="checkbox" name="qu_aos_settings[disable_mobile]" value="1"
                                <?php checked( $settings['disable_mobile'], '1' ); ?>>
                            모바일 접속 시 AOS 애니메이션을 비활성화
                        </label>
                        <p class="description">
                            모바일에서는 성능과 UX를 위해 스크롤 애니메이션을 끄는 것이 일반적입니다.
                            체크하면 <code>wp_is_mobile()</code>이 true일 때 AOS 스크립트 자체를 로드하지 않습니다.
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="qu_aos_duration">duration (ms)</label></th>
                    <td>
                        <input type="number" name="qu_aos_settings[duration]" id="qu_aos_duration"
                               value="<?php echo esc_attr( $settings['duration'] ); ?>"
                               placeholder="예: 1000" min="50" max="3000" step="50">
                        <p class="description">
                            애니메이션 지속 시간 (밀리초). 값을 비워두면 이 플러그인의 기본값인 <strong>1000ms</strong>가 적용됩니다.
                            (권장 범위: 50 ~ 3000, 50ms 단위)
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="qu_aos_easing">easing</label></th>
                    <td>
                        <input type="text" name="qu_aos_settings[easing]" id="qu_aos_easing"
                               value="<?php echo esc_attr( $settings['easing'] ); ?>"
                               placeholder="예: ease-out-cubic">
                        <p class="description">애니메이션 이징(easing) 함수. 비워두면 기본값.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="qu_aos_offset">offset (px)</label></th>
                    <td>
                        <input type="number" name="qu_aos_settings[offset]" id="qu_aos_offset"
                               value="<?php echo esc_attr( $settings['offset'] ); ?>"
                               placeholder="예: 120">
                        <p class="description">요소가 화면 안으로 어느 정도 들어왔을 때 애니메이션을 시작할지(px). 비워두면 기본값.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="qu_aos_delay">delay (ms)</label></th>
                    <td>
                        <input type="number" name="qu_aos_settings[delay]" id="qu_aos_delay"
                               value="<?php echo esc_attr( $settings['delay'] ); ?>"
                               placeholder="예: 200" step="50">
                        <p class="description">
                            공통 지연 시간(밀리초). 개별 요소의 <code>data-aos-delay</code>가 더 우선합니다.
                            값을 비워두면 이 옵션은 <strong>init에 포함되지 않습니다</strong>. (권장 범위: 50 ~ 3000, 50ms 단위)
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">once</th>
                    <td>
                        <label>
                            <input type="checkbox" name="qu_aos_settings[once]" value="1"
                                <?php checked( $settings['once'], '1' ); ?>>
                            한 번만 실행 (스크롤을 다시 올려도 애니메이션 반복 없음)
                        </label>
                        <p class="description">체크하지 않으면 <code>once</code> 옵션은 init에 포함되지 않습니다.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">mirror</th>
                    <td>
                        <label>
                            <input type="checkbox" name="qu_aos_settings[mirror]" value="1"
                                <?php checked( $settings['mirror'], '1' ); ?>>
                            스크롤을 위로 올릴 때도 애니메이션 실행
                        </label>
                        <p class="description">대부분의 경우 비활성(체크 해제)을 추천합니다. 체크하지 않으면 init에 포함되지 않습니다.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>

        <h2>AOS data-attribute 정리</h2>
        <p>아래 속성들은 각 블록 / 요소에 직접 지정할 수 있는 옵션입니다. GenerateBlocks 또는 블록 에디터의 “HTML 속성”에 입력해서 사용하면 됩니다.</p>

        <table class="widefat striped" style="max-width: 900px;">
            <thead>
                <tr>
                    <th style="width: 20%;">속성</th>
                    <th style="width: 25%;">예시 값</th>
                    <th>설명</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>data-aos</code></td>
                    <td><code>fade-up</code>, <code>fade-right</code>, <code>zoom-in</code></td>
                    <td>어떤 애니메이션 효과를 사용할지 지정합니다.<br/>
                    예: <code>fade-up</code>, <code>fade-down</code>, <code>fade-left</code>, <code>fade-right</code>, <code>fade-up-left</code>, <code>fade-up-right</code>, <code>fade-down-left</code>, <code>fade-down-right</code>,<br/>
                        <code>zoom-in</code>, <code>zoom-in-up</code>, <code>zoom-in-down</code>, <code>zoom-in-right</code>, <code>zoom-in-left</code>, <code>zoom-out</code>, <code>zoom-out-up</code>, <code>zoom-out-down</code>, <code>zoom-out-right</code>, <code>zoom-out-left</code>, <br/> 
                        <code>flip-left</code>, <code>flip-right</code>, <code>flip-up</code>, <code>flip-down</code> </td>
                </tr>
                <tr>
                    <td><code>data-aos-delay</code></td>
                    <td><code>200</code></td>
                    <td>해당 요소에만 적용되는 지연 시간(ms). 예: 200이면 0.2초 후 실행.</td>
                </tr>
                <tr>
                    <td><code>data-aos-duration</code></td>
                    <td><code>1000</code></td>
                    <td>해당 요소의 애니메이션 지속 시간(ms). 전역 <code>duration</code>보다 우선합니다.</td>
                </tr>
                <tr>
                    <td><code>data-aos-offset</code></td>
                    <td><code>300</code></td>
                    <td>요소가 화면에 어느 정도 들어왔을 때 시작할지(px). 전역 <code>offset</code>보다 우선합니다.</td>
                </tr>
                <tr>
                    <td><code>data-aos-easing</code></td>
                    <td><code>ease-in-sine</code></td>
                    <td>해당 요소의 easing. 예: <code>ease</code>, <code>ease-in</code>, <code>ease-out</code>, <code>ease-in-sine</code> 등.</td>
                </tr>
                <tr>
                    <td><code>data-aos-anchor</code></td>
                    <td><code>.hero-section</code></td>
                    <td>이 요소가 아니라 다른 요소를 기준(anchor)으로 스크롤 위치를 계산하고 싶을 때 사용합니다.</td>
                </tr>
                <tr>
                    <td><code>data-aos-anchor-placement</code></td>
                    <td><code>top-bottom</code></td>
                    <td>anchor 기준으로 어느 지점에서 트리거할지 지정. 예: <code>top-bottom</code>, <code>center-bottom</code>, <code>bottom-bottom</code> 등.</td>
                </tr>
                <tr>
                    <td><code>data-aos-once</code></td>
                    <td><code>true</code> / <code>false</code></td>
                    <td>해당 요소에 대해서만 1회 실행 여부를 제어합니다. 전역 <code>once</code> 옵션보다 우선합니다.</td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 1em;">
            예) GenerateBlocks -> 고급 -> Custom Attributes 추가<br>
            <code>data-aos="fade-up" data-aos-delay="200"</code>
        </p>
    </div>
    <?php
}
