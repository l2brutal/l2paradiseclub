<?php


namespace IPS\bdmoods\modules\admin\manage;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * manage
 */
class _manage extends \IPS\Dispatcher\Controller
{	
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'bdmoods_manage' );
		parent::execute();
	}
	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{		
		/* Create the table */
		$table = new \IPS\Helpers\Table\Db( 'bdmoods_moods', \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage' ) );

        /* Table Options */
        $table->include = array( 'mood_title', 'mood_image');
		$table->mainColumn = 'mood_title';
        $table->noSort	= array( 'mood_image' );
        $table->quickSearch = 'mood_title';
        $table->langPrefix = 'bd_moods_';
        
        /* Add Button */
        $table->rootButtons = array(
            'bulkAdd'	=> array(
                'icon'		=> 'cube',
                'title'		=> 'bd_moods_addBulkMood',
                'link'		=> \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage&do=addBulk' ),
                'data'		=> array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('bd_moods_addBulkMood') )
            ),
            'add'	=> array(
                'icon'		=> 'plus',
                'title'		=> 'bd_moods_addMood',
                'link'		=> \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage&do=add' ),
                'data'		=> array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('bd_moods_addMood') )
            )
        );
        
        /* Table Row Buttons */
        $table->rowButtons = function( $row ) {
            $mood = \IPS\bdmoods\Mood::constructFromData( $row );
            
            $return = array();
            $return['edit'] = array(
					'icon'		=> 'pencil',
					'title'		=> 'edit',
					'link'		=> \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage&do=edit&id=' ). $mood->mood_id,
					'hotkey'	=> 'e',
                    'data'		=> array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('bd_moods_editMood') )
            );
            $return['delete'] = array(
					'icon'		=> 'times-circle',
					'title'		=> 'delete',
					'link'		=> \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage&do=delete&id=' ). $mood->mood_id,
                    'data'      => array( 'delete' => '' )
            );
            return $return;
        };
        
        /* Parse the Image show it shows */
        $table->parsers = array(
			'mood_image'=> function( $val, $row ) {
                $mood = \IPS\bdmoods\Mood::constructFromData( $row );
				return '<img src="'. \IPS\File::get( 'bdmoods_FileStorage', $mood->mood_image )->url.'" alt="'.$mood->mood_title.' Image"/>';
			}
        );

		/* Display */
        \IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack('bd_moods_manageMoods');
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}
    
    public function edit() {
        
		try
		{
			$mood	= \IPS\bdmoods\Mood::load( \IPS\Request::i()->id );
		}
		catch( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'bd_moods_moodNotFound', '2L169/2', 404, '' );
		}
        
         /* Build form */
		$form = new \IPS\Helpers\Form;
		$form->add( new \IPS\Helpers\Form\Text( 'bd_moods_mood_title', $mood->mood_title, TRUE ) );
        $form->add( new \IPS\Helpers\Form\Upload( 'bd_moods_mood_image', ( $mood AND $mood->mood_image ) ? \IPS\File::get( 'bdmoods_mood', $mood->mood_image) : NULL, TRUE, array( 'storageExtension' => 'bdmoods_mood', 'multiple' => false, 'image' => array( 'maxWidth' => 96, 'maxHeight' => 96 ),'maxFileSize'=> 1 ), NULL, NULL, NULL, 'mood_image' ) );
        
        /* Handle submissions */
		if ( $values = $form->values() ) {

	       \IPS\Db::i()->update( 'bdmoods_moods',array( 'mood_title' => (string) $values['bd_moods_mood_title'], 'mood_image'=> (string) $values['bd_moods_mood_image']), array( 'mood_id=?',$mood->mood_id) );
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage' ), 'saved' );
		}
		
		/* Display */
		if ( \IPS\Request::i()->isAjax() ) {
			\IPS\Output::i()->outputTemplate = array( \IPS\Theme::i()->getTemplate( 'global', 'core' ), 'blankTemplate' );
		}
		\IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack( 'bd_moods_EditMood' );
		\IPS\Output::i()->output = $form;
    }
    
    public function delete() {
        
        try
		{
			$mood	= \IPS\bdmoods\Mood::load( \IPS\Request::i()->id );
            \IPS\Db::i()->update( 'core_members',array('bdm_mood'=>null),array('bdm_mood=?',$mood->mood_id));
            $mood->delete();
            // Remove the logs
            \IPS\Db::i()->delete('bdmoods_updates',array('update_mood=?',$mood->mood_id));
            // Remove the file
            \IPS\File::get( 'bdmoods_FileStorage', $mood->mood_image )->delete();
		}
		catch( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'bd_moods_moodNotFound', '2L169/2', 404, '' );
		}
        
        
    }
    
    public function add() {
        
        /* Build form */
		$form = new \IPS\Helpers\Form;
		$form->add( new \IPS\Helpers\Form\Text( 'bd_moods_mood_title', NULL, TRUE ) );
        $form->add( new \IPS\Helpers\Form\Upload( 'bd_moods_mood_image', NULL, TRUE, array( 'storageExtension' => 'bdmoods_FileStorage', 'multiple' => false, 'image' => array( 'maxWidth' => 96, 'maxHeight' => 96 ),'maxFileSize'=> 1 ), NULL, NULL, NULL, 'mood_image' ) );
        
        /* Handle submissions */
		if ( $values = $form->values() ) {

	       \IPS\Db::i()->insert( 'bdmoods_moods', array( 'mood_title'=>  (string) $values['bd_moods_mood_title'] , 'mood_image' => (string) $values['bd_moods_mood_image'] ) );
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage' ), 'saved' );
		}
		
		/* Display */
		if ( \IPS\Request::i()->isAjax() ) {
			\IPS\Output::i()->outputTemplate = array( \IPS\Theme::i()->getTemplate( 'global', 'core' ), 'blankTemplate' );
		}
		\IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack( 'bd_moods_addMood' );
		\IPS\Output::i()->output = $form;
    }
    
    public function addBulk() {
        
        /* Build form */
		$form = new \IPS\Helpers\Form;
        $form->add( new \IPS\Helpers\Form\Upload( 'bd_moods_mood_images', NULL, TRUE, array( 'storageExtension' => 'bdmoods_FileStorage', 'multiple' =>true, 'image' => array( 'maxWidth' => 96, 'maxHeight' => 96 ),'maxFileSize'=> 1 ), NULL, NULL, NULL, 'mood_image' ) );
        
        /* Handle submissions */
		if ( $values = $form->values() ) {
            foreach ($values['bd_moods_mood_images'] as $image) {
                $info = pathinfo($image->originalFilename);
                $name = (empty($info['filename']) ? "Unkown" : $info['filename']);
                
	           \IPS\Db::i()->insert( 'bdmoods_moods', array( 'mood_title'=>  $name , 'mood_image' => $image ) );
                
            }
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=bdmoods&module=manage&controller=manage' ), 'saved' );
		}
		
		/* Display */
		if ( \IPS\Request::i()->isAjax() ) {
			\IPS\Output::i()->outputTemplate = array( \IPS\Theme::i()->getTemplate( 'global', 'core' ), 'blankTemplate' );
		}
		\IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack( 'bd_moods_addBulkMood' );
		\IPS\Output::i()->output = $form;
    }
}