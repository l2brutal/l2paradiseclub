<?php

/**
 * @brief       Fmlist Class
 * @author      <a href='http://www.bbcode.it'>InvisionHQ - G. Venturini</a>
 * @copyright   (c) 2016 InvisionHQ - G. Venturini
 * @package     IPS Social Suite
 * @subpackage  Forums MakeUP
 * @since       
 * @version     1.0.0
 */

namespace IPS\fmakeup\modules\admin\main;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * fmlist
 */
class _fmlist extends \IPS\Dispatcher\Controller
{	
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'fmlist_manage' );
		parent::execute();
	}

    /**
     * Toggle a Makeup state to active or inactive
     *
     * @note	Nothing
     * @return	void
     */
    protected function toggle()
    {
        /* Get our record */
        try
        {
            $record	= \IPS\fmakeup\Lista::load( \IPS\Request::i()->id );
        }
        catch( \OutOfRangeException $e )
        {
            \IPS\Output::i()->error( 'node_error', '2C157/5', 404, '' );
        }

        /* Toggle the record */
        if ( \IPS\Db::i()->select( 'COUNT(*)', 'forums_forums', 'id='.\IPS\Request::i()->id )->first() )
        {
            $record->fmakeup_status = (int) \IPS\Request::i()->status;
            $record->save();

            /* Log and redirect */
            if ( $record->fmakeup_status == -1 )
            {
                \IPS\Session::i()->log( 'acplog_badge_approved', [ ] );
            }
            else
            {
                if ( $record->fmakeup_status == 1 )
                {
                    \IPS\Session::i()->log( 'acplog_makeup_badge_enabled', [ ] );
                }
                else
                {
                    \IPS\Session::i()->log( 'acplog_makeup_badge_disabled', [ ] );
                }
            }

            \IPS\Output::i()->redirect(
                \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ),
                \IPS\Request::i()->status ? 'fmakeup_toggled_visible' : 'fmakeup_toggled_notvisible'
            );
        }
    }

    /**
     * Toggle a Makeup state to active or inactive
     *
     * @note	Nothing
     * @return	void
     */
    protected function toggleGrid()
    {
        /* Get our record */
        try
        {
            $record	= \IPS\fmakeup\Lista::load( \IPS\Request::i()->id );
        }
        catch( \OutOfRangeException $e )
        {
            \IPS\Output::i()->error( 'node_error', '2C157/5', 404, '' );
        }

        /* Toggle the record */
        if ( \IPS\Db::i()->select( 'COUNT(*)', 'forums_forums', 'id='.\IPS\Request::i()->id )->first() )
        {
            $record->fmakeup_grid = (int) \IPS\Request::i()->grid;
            $record->save();

            /* Log and redirect */
            if ( $record->fmakeup_grid == -1 )
            {
                \IPS\Session::i()->log( 'acplog_grid_added', [ ] );
            }
            else
            {
                if ( $record->fmakeup_grid == 1 )
                {
                    \IPS\Session::i()->log( 'acplog_grid_makeup_enabled', [ ] );
                }
                else
                {
                    \IPS\Session::i()->log( 'acplog_grid_makeup_disabled', [ ] );
                }
            }

            \IPS\Output::i()->redirect(
                \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ),
                \IPS\Request::i()->grid ? 'fmakeup_toggled_visible' : 'fmakeup_toggled_notvisible'
            );
        }
    }

    /**
     * Delete a Badge
     *
     * @return	void
     */
    protected function delete()
    {
        /* Permission check */
        \IPS\Dispatcher::i()->checkAcpPermission( 'fmakeup_delete' );

        /* Get our record */
        try
        {
            $record	= \IPS\fmakeup\Lista::load( \IPS\Request::i()->id );
        }
        catch( \OutOfRangeException $e )
        {
            \IPS\Output::i()->error( 'node_error', '2C157/2', 404, '' );
        }

        /* Delete the record */

        $record->fmakeup_status	= NULL;
        $record->fmakeup_text	= NULL;
        $record->fmakeup_color  = NULL;
        $record->fmakeup_bg     = NULL;
        $record->fmakeup_border = NULL;
        $record->fmakeup_grid   = NULL;

        // Log
        \IPS\Session::i()->log( 'acplog_fmakeup_deleted', array() );

        $record->save();

        /* Redirect */
        \IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ), 'deleted' );
    }


    /**
     * Add/edit a Makeup Badge
     *
     * @return	void
     */
    protected function formBadge()
    {
        /* Are we editing? */
        if( isset( \IPS\Request::i()->id ) )
        {
            try
            {
                $record	= \IPS\fmakeup\Lista::load( \IPS\Request::i()->id );
            }
            catch( \OutOfRangeException $e )
            {
                \IPS\Output::i()->error( 'node_error', '2C157/1', 404, '' );
            }
        }
        else
        {
            $record = new \IPS\fmakeup\Lista;
        }

        /* Start the form */
        $form	= new \IPS\Helpers\Form;

        /* Show the fields for a badge makeup */
        if (!$record->id)
        {
            $form->addHeader( 'fmakeup_forum' );
            $form->add( new \IPS\Helpers\Form\Node( 'forums', ( $record->id ) ? $record->id : 1, FALSE, array( 'class' => 'IPS\forums\Forum', 'multiple' => FALSE) ) );
        }
        $form->addHeader( 'fmakeup_badge' );
        $form->add( new \IPS\Helpers\Form\Text( 'fmakeup_text', ( $record->id ) ? $record->fmakeup_text : NULL, TRUE, array(), NULL, NULL, NULL, 'fmakeup_text' ) );
        $form->add( new \IPS\Helpers\Form\Color( 'fmakeup_color', ( $record->id ) ? $record->fmakeup_color : NULL, TRUE, array(), NULL, NULL, NULL, 'fmakeup_color' ) );
        /* Handle submissions */
        if ( $values = $form->values() )
        {
            //echo "<pre>";
            //print_r($values['forums']->id);
            //exit;
            /* Insert or update */
            if( $record->id )
            {
                \IPS\Session::i()->log( 'acplog_fmakeup_badge_edited', array() );
            }
            else
            {
                $record	= \IPS\fmakeup\Lista::load( $values['forums']->id );
                \IPS\Session::i()->log( 'acplog_fmakeup_badge_added', array() );
            }

            /* Let us start with the easy stuff... */
            $record->fmakeup_status			= ( $record->fmakeup_status ) ? $record->fmakeup_status : 1;
            $record->fmakeup_text    		= $values['fmakeup_text'];
            $record->fmakeup_color      	= $values['fmakeup_color'];

            $record->save();

            /* Redirect */
            \IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ), 'saved' );
        }

        \IPS\Output::i()->title		= \IPS\Member::loggedIn()->language()->addToStack( ( !isset( \IPS\Request::i()->id ) ) ? 'add_fmakeup_badge' : 'edit_fmakeup_badge' );
        \IPS\Output::i()->output 	= \IPS\Theme::i()->getTemplate('global')->block( ( !isset( \IPS\Request::i()->id ) ) ? 'add_badge' : 'edit_badge', $form );
    }


    /**
     * Set a category Grid
     *
     * @return	void
     */
    protected function formGrid()
    {
        $record = new \IPS\fmakeup\Lista;

        /* Start the form */
        $form	= new \IPS\Helpers\Form;

        $form->addHeader( 'fmakeup_forum' );
        $form->add( new \IPS\Helpers\Form\Node( 'fmu_category', ( $record->id ) ? $record->id : 1, TRUE, array( 'class' => 'IPS\forums\Forum', 'multiple' => FALSE, 'where' => array(array('parent_id=?', -1)) ) ) );

        /* Handle submissions */
        if ( $values = $form->values() )
        {
            $record	= \IPS\fmakeup\Lista::load( $values['fmu_category']->id );
            \IPS\Session::i()->log( 'acplog_fmakeup_badge_added', array() );

            /* Let us start with the easy stuff... */
            $record->fmakeup_status			= ( $record->fmakeup_status ) ? $record->fmakeup_status : 1;
            $record->fmakeup_grid    		= 1;

            $record->save();

            /* Redirect */
            \IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ), 'saved' );
        }

        \IPS\Output::i()->title		= \IPS\Member::loggedIn()->language()->addToStack( 'fmakeup_set_grid' );
        \IPS\Output::i()->output 	= \IPS\Theme::i()->getTemplate('global')->block( 'set_grid', $form );
    }

    /**
     * Add/edit a Makeup Container
     *
     * @return	void
     */
    protected function formContainer()
    {
        /* Are we editing? */
        if( isset( \IPS\Request::i()->id ) )
        {
            try
            {
                $record	= \IPS\fmakeup\Lista::load( \IPS\Request::i()->id );
            }
            catch( \OutOfRangeException $e )
            {
                \IPS\Output::i()->error( 'node_error', '2C157/1', 404, '' );
            }
        }
        else
        {
            $record = new \IPS\fmakeup\Lista;
        }

        /* Start the form */
        $form	= new \IPS\Helpers\Form;

        /* Show the fields for a badge makeup */
        if (!$record->id)
        {
            $form->addHeader( 'fmakeup_forum' );
            $form->add( new \IPS\Helpers\Form\Node( 'forums', ( $record->id ) ? $record->id : 1, FALSE, array( 'class' => 'IPS\forums\Forum', 'multiple' => FALSE) ) );
        }
        $form->addHeader( 'fmakeup_container' );
        $form->add( new \IPS\Helpers\Form\Color( 'fmakeup_bg', ( $record->id ) ? $record->fmakeup_bg : NULL, TRUE, array(), NULL, NULL, NULL, 'fmakeup_bg' ) );
	    if (\IPS\Settings::i()->fmu_border_on)
        $form->add( new \IPS\Helpers\Form\Color( 'fmakeup_border', ( $record->id ) ? $record->fmakeup_border : NULL, TRUE, array(), NULL, NULL, NULL, 'fmakeup_border' ) );
        /* Handle submissions */
        if ( $values = $form->values() )
        {
            //echo "<pre>";
            //print_r($values['forums']->id);
            //exit;
            /* Insert or update */
            if( $record->id )
            {
                \IPS\Session::i()->log( 'acplog_fmakeup_container_edited', array() );
            }
            else
            {
                $record	= \IPS\fmakeup\Lista::load( $values['forums']->id );
                \IPS\Session::i()->log( 'acplog_fmakeup_container_added', array() );
            }

            /* Let us start with the easy stuff... */
            $record->fmakeup_status			= ( $record->fmakeup_status ) ? $record->fmakeup_status : 1;
            $record->fmakeup_bg    		    = $values['fmakeup_bg'];
	        if (\IPS\Settings::i()->fmu_border_on)
            $record->fmakeup_border     	= $values['fmakeup_border'];

            $record->save();

            /* Redirect */
            \IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ), 'saved' );
        }

        \IPS\Output::i()->title		= \IPS\Member::loggedIn()->language()->addToStack( ( !isset( \IPS\Request::i()->id ) ) ? 'add_fmakeup_container' : 'edit_fmakeup_container' );
        \IPS\Output::i()->output 	= \IPS\Theme::i()->getTemplate('global')->block( ( !isset( \IPS\Request::i()->id ) ) ? 'add_container' : 'edit_container', $form );
    }
	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{
        \IPS\Output::i()->sidebar['actions'] = array(
            'settings_container'	=> array(
                'icon'	            => 'cog',
                'link'          	=> \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=settingsContainer' ),
                'title'         	=> 'Container Settings',
            ),
            'settings_badge'	=> array(
                'icon'	        => 'cog',
                'link'	        => \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=settingsBadge' ),
                'title'     	=> 'Badge Settings',
            ),
        );

		/* Create the table */
        $where = array( array( 'fmakeup_status >= ?', 0 ) );
		$table = new \IPS\Helpers\Table\Db( 'forums_forums', \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist' ), $where );
        /* Columns */
        $table->joins = array(
            array( 'select' => 'w.word_custom as forum_name', 'from' => array( 'core_sys_lang_words', 'w' ), 'where' => "w.word_key=CONCAT( 'forums_forum_', forums_forums.id ) AND w.lang_id=" . \IPS\Member::loggedIn()->language()->id )
        );
        $table->langPrefix = 'fmu_';

        /* Columns we need */
        $table->include = array( 'type', 'name_seo', 'container', 'badge', 'fmakeup_grid', 'fmakeup_status' );
        $table->mainColumn = 'name_seo';
        $table->noSort	= array( 'type','fmakeup_status' );
        $table->widths = array( 'type' => '1', 'name' => '52', 'fmakeup_status' => '1' );

        /* Default sort options */
        $table->sortBy = $table->sortBy ?: 'id';
        $table->sortDirection = $table->sortDirection ?: 'desc';

        /* Filters */
        $table->filters = array(
            'fmu_filters_active'			=> 'fmakeup_status=1',
            'fmu_filters_inactive'			=> 'fmakeup_status=0',
            'fmu_filters_category'			=> 'parent_id=-1',
            );

        /* Custom parsers */
        $table->parsers = array(
            'type'              => function( $val, $row )
            {
                if ($row['parent_id'] == -1)
                {
                    $tipo = "<span class=\"ipsBadge ipsBadge_style2 ipsBadge ipsBadge_icon ipsType_center\"><i class=\"fa fa-comments\"></i></span>";
                } else {
                    $tipo = "<span class=\"ipsBadge ipsBadge_style1 ipsBadge ipsBadge_icon ipsType_center\"><i class=\"fa fa-comment\"></i></span>";
                }
                return $tipo;
            },
            'name_seo'			=> function( $val, $row )
            {
                return "<h1 class=\"ipsType_sectionHead\">".$row['forum_name']."</h1>";
            },
            'container'		    => function( $val, $row )
            {
                if (isset($row['fmakeup_bg']))
                {
                	if (\IPS\Settings::i()->fmu_border_on) {
                		$border = 'border: '.\IPS\Settings::i()->fmu_border_size.'px solid '.$row['fmakeup_border'];
	                } else {
	                	$border = '';
	                }
                    return "<span class=\"ipsBadge ipsBadge_intermediary\" style=\"".$border.";width:50px;height:40px;background-color: ".$row['fmakeup_bg']."\"></span> <a href=\"".\IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=formContainer&id=' . $row['id'] )."\"><span class=\"fa fa-pencil\"></span></a>";
                } else {
                    $what = 'formContainer';
                    return \IPS\Theme::i()->getTemplate( 'main' )->add( $row['id'], $what );
                }
            },
            'badge'		    	=> function( $val, $row )
            {
                if (isset($row['fmakeup_text']))
                {
                    return "<span class=\"ipsBadge ipsBadge_intermediary\" style=\"background-color: ".$row['fmakeup_color']."\">".$row['fmakeup_text']."</span> <a href=\"".\IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=formBadge&id=' . $row['id'] )."\"><span class=\"fa fa-pencil\"></span></a>";
                } else {
                    $what = "formBadge";
                    return \IPS\Theme::i()->getTemplate( 'main' )->add( $row['id'], $what );
                }

            },
            'fmakeup_grid'	=> function( $val, $row )
            {
                if ($row['parent_id'] == -1) {
                	    if (!isset($val)) $val = 0;
                        return \IPS\Theme::i()->getTemplate('main')->activeGrid($row['id'], ($val == 0) ? 'grid_filters_inactive' : 'grid_filters_active', $val);
                } else {
                    $title = 'grid makeup available only in category';
                    return \IPS\Theme::i()->getTemplate( 'main' )->unavailable( $title );
                }
            },
            'fmakeup_status'	=> function( $val, $row )
            {
                return \IPS\Theme::i()->getTemplate( 'main' )->activeMakeup( $row['id'],  ( $val == 0 ) ? 'badge_filters_inactive' : 'badge_filters_active', $val );
            },
        );

        /* Specify the buttons */
        if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'fmakeup', 'main', 'fmlist' ) )
        {
            $table->rootButtons = array(
                'add'	=> array(
                    'icon'		=> 'plus',
                    'title'		=> 'fmakeup_add_badge',
                    'link'		=> \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=formBadge' ),
                ),
                'addbg'	=> array(
                    'icon'		=> 'object-group',
                    'title'		=> 'fmakeup_container',
                    'link'		=> \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=formContainer' ),
                ),
                'setGrid'	=> array(
                    'icon'		=> 'th',
                    'title'		=> 'fmakeup_set_grid',
                    'link'		=> \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=formGrid' ),
                )
            );
        }

        $table->rowButtons = function( $row )
        {
            $return = array();

            if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'fmakeup', 'main', 'fmlist' ) )
            {
                $return['delete'] = array(
                    'icon'		=> 'times-circle',
                    'title'		=> 'delete',
                    'link'		=> \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=fmlist&do=delete&id=' . $row['id'] ),
                    'data'		=> array( 'delete' => '' ),
                );
            }

            return $return;
        };

		/* Display */
        \IPS\Output::i()->title		= \IPS\Member::loggedIn()->language()->addToStack('fmakeup_list');
		\IPS\Output::i()->output	= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}

	/**
	 * Container Settings
	 *
	 * @return	void
	 */
	protected function settingsContainer()
	{
		$form = new \IPS\Helpers\Form;

		$form->add( new \IPS\Helpers\Form\YesNo('fmu_border_on',\IPS\Settings::i()->fmu_border_on,FALSE ) );
		$form->add( new \IPS\Helpers\Form\Number('fmu_border_size', \IPS\Settings::i()->fmu_border_size, FALSE ) );


		if ( $values = $form->values() )
		{
			$form->saveAsSettings( $values );
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=fmakeup&module=main&controller=fmlist" ), 'saved' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('fmu_container_settings');
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate('global')->block( 'fmu_container_settings', $form );
	}

    /**
     * Badge Settings
     *
     * @return	void
     */
    protected function settingsBadge()
    {
        $form = new \IPS\Helpers\Form;

        $form->add( new \IPS\Helpers\Form\Select( 'fmu_badge_size', \IPS\Settings::i()->fmu_badge_size, FALSE, array( 'options' => array('large' =>  'Large', 'Medium' => 'medium', 'small' => 'Small') ), NULL, NULL, NULL, 'fmu_badge_size' ) );

        if ( $values = $form->values() )
        {
            $form->saveAsSettings( $values );
            \IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=fmakeup&module=main&controller=fmlist" ), 'saved' );
        }

        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('fmu_badge_settings');
        \IPS\Output::i()->output = \IPS\Theme::i()->getTemplate('global')->block( 'fmu_badge_settings', $form );
    }
}
