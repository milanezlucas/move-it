<?php
$sidebars = array(
    'sidebar-test'  => array(
        'name'  => 'Teste',
        'desc'  => 'Sidebar de Teste'
    ),
);

foreach ( $sidebars as $id => $side ) {
    register_sidebar(array(
        'name'            => $side[ 'name' ],
        'id'              => $id,
        'description'     => $side[ 'desc' ],
        'before_title'    => '',
        'after_title'     => ''
    ));
}

function unregister_widgets()
{
    $unregister = array(
        'WP_Widget_Archives',
        'WP_Widget_Calendar',
        'WP_Widget_Categories',
        'WP_Widget_Links',
        'WP_Widget_Meta',
        'WP_Widget_Pages',
        'WP_Widget_Recent_Comments',
        'WP_Widget_Recent_Posts',
        'WP_Widget_RSS',
        'WP_Widget_Search',
        'WP_Widget_Tag_Cloud',
        'WP_Widget_Text',
        'WP_Nav_Menu_Widget'
    );
    for ( $i=0; $i < count( $unregister ); $i++ ) {
        unregister_widget( $unregister[ $i ] );
    }
}

function register_widgets()
{
    $register = array(
        'MI_Test_Sidebar'
    );
    for ( $i=0; $i < count( $register ); $i++ ) {
        register_widget( $register[ $i ] );
    }
}

// Test
class MI_Test_Sidebar extends WP_Widget
{
    public function MI_Test_Sidebar()
    {
        WP_Widget::__construct( 'teste', 'Teste', array( 'description' => 'Descrição do widget' ) );
    }

    public function widget( $args, $inst )
    {
        $html .= '';

        echo $html;
    }

    public function update( $new, $old )
    {
        return array_merge( $old, $new );
    }

    public function form( $inst )
    {
        $fields = array(
            $this->get_field_name( 'text' )  => array(
                'name'      => 'text',
                'type'      => 'text',
                'label'     => 'Text',
                'desc'      => 'Campo de Texto',
            ),
            $this->get_field_name( 'textarea' )  => array(
                'name'      => 'textarea',
                'type'      => 'textarea',
                'label'     => 'Textarea',
                'desc'      => 'Campo Textarea',
                'required'  => true
            ),
            $this->get_field_name( 'date' )  => array(
                'name'      => 'date',
                'type'      => 'date',
                'label'     => 'Date',
                'desc'      => 'Campo Data',
                'required'  => true
            ),
            $this->get_field_name( 'select' )    => array(
                'name'      => 'select',
                'type'      => 'select',
                'label'     => 'Select',
                'desc'      => 'Campo Select',
                'required'  => true,
                'opt'       => array(
                    '1' => 'Value 1',
                    '2' => 'Value 2'
                )
            ),
            $this->get_field_name( 'multiple' )  => array(
                'name'      => 'multiple',
                'type'      => 'select',
                'label'     => 'Select',
                'desc'      => 'Campo Multiple',
                'required'  => true,
                'multiple'  => true,
                'opt'       => array(
                    '1' => 'Value 1',
                    '2' => 'Value 2'
                )
            ),
            $this->get_field_name( 'radio' ) => array(
                'name'  => 'radio',
                'type'  => 'radio',
                'label' => 'Radio',
                'desc'  => 'Campo Radio',
                'opt'   => array(
                    '1' => 'Value 1',
                    '2' => 'Valu 2'
                )
            ),
            $this->get_field_name( 'checkbox' )  => array(
                'name'  => 'checkbox',
                'type'  => 'checkbox',
                'label' => 'Checkbox',
                'desc'  => 'Campo Checkbox',
                'opt'   => array(
                    '1' => 'Value 1',
                    '2' => 'Value 2'
                )
            ),
            $this->get_field_name( 'number' )    => array(
                'name'      => 'number',
                'type'      => 'number',
                'label'     => 'Number',
                'desc'      => 'Campo Number',
                'required'  => true,
                'min'       => '1',
                'max'       => '10'
            ),
            $this->get_field_name( 'file' )  => array(
                'name'  => 'file',
                'type'  => 'file',
                'label' => 'File',
                'desc'  => 'Campo File'
            )
        );

        $mi_forms = new MI_Forms;
        foreach ( $fields as $id => $field ) {
            $form = $mi_forms->field( $id, $field, stripslashes( $inst[ $field[ 'name' ] ] ) );
            $html .= '
                <p>
                   <label for="'. $form->name . '">' . $form->label . '</label>
                   ' . $form->field . '
            ';
            $html .= $form->desc ? '<i class="description">' . $form->desc . '</i>' : '';
            $html .= '
                </p>
            ';
        }

        echo $html;
    }
}
?>
