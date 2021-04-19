<?php
namespace AIOSEO\Plugin\Common\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the static sitemap.
 *
 * @since 4.0.0
 */
class File {

	/**
	 * Whether the static files have already been updated during the current request.
	 *
	 * We keep track of this so that setting changes to do not trigger the regeneration multiple times.
	 *
	 * @since 4.0.0
	 *
	 * @var boolean
	 */
	private static $isUpdated = false;

	/**
	 * Generates the static sitemap files.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $force Whether or not to force it through.
	 * @return void
	 */
	public function generate( $force = false ) {
		foreach ( aioseo()->sitemap->addons as $addon => $classes ) {
			if ( ! empty( $classes['file'] ) ) {
				$classes['file']->generate( $force );
			}
		}

		// Exit if static sitemap generation isn't enabled.
		if (
			! $force &&
			(
				self::$isUpdated ||
				! aioseo()->options->sitemap->general->enable ||
				! aioseo()->options->sitemap->general->advancedSettings->enable ||
				! in_array( 'staticSitemap', aioseo()->internalOptions->internal->deprecatedOptions, true ) ||
				aioseo()->options->deprecated->sitemap->general->advancedSettings->dynamic
			)
		) {
			return;
		}

		$files           = [];
		self::$isUpdated = true;
		// We need to set these values here as determineContext() doesn't run.
		// Subsequently, we need to manually reset the index name below for each query we run.
		// Also, since we need to chunck the entries manually, we cannot limit any queries and need to reset the amount of allowed URLs per index.
		aioseo()->sitemap->offset        = 0;
		aioseo()->sitemap->type          = 'general';
		$sitemapName                     = aioseo()->sitemap->helpers->filename();
		aioseo()->sitemap->indexes       = aioseo()->options->sitemap->general->indexes;
		aioseo()->sitemap->linksPerIndex = PHP_INT_MAX;

		$pages = [];
		foreach ( aioseo()->options->sitemap->general->additionalPages->pages as $page ) {
			$additionalPage = json_decode( $page );
			if ( empty( $additionalPage->url ) ) {
				continue;
			}

			$pages[] = $additionalPage;
		}

		if (
			'posts' === get_option( 'show_on_front' ) ||
			( aioseo()->options->sitemap->general->additionalPages->enable && count( $pages ) )
		) {
			$filename           = "addl-$sitemapName.xml";
			$files[ $filename ] = aioseo()->sitemap->content->addl();
		}

		if (
			aioseo()->sitemap->helpers->lastModifiedPost() &&
			aioseo()->options->sitemap->general->author
		) {
			$filename           = "author-$sitemapName.xml";
			$files[ $filename ] = aioseo()->sitemap->content->author();
		}

		if (
			aioseo()->sitemap->helpers->lastModifiedPost() &&
			aioseo()->options->sitemap->general->date
		) {
			$filename           = "date-$sitemapName.xml";
			$files[ $filename ] = aioseo()->sitemap->content->date();
		}

		$postTypes = aioseo()->sitemap->helpers->includedPostTypes();
		if ( $postTypes ) {
			foreach ( $postTypes as $postType ) {
				aioseo()->sitemap->indexName = $postType;

				$posts = aioseo()->sitemap->content->posts( $postType );
				if ( ! $posts ) {
					continue;
				}

				$chunks = aioseo()->sitemap->helpers->chunkEntries( $posts );
				if ( 1 === count( $chunks ) ) {
					$filename           = "$postType-$sitemapName.xml";
					$files[ $filename ] = $chunks[0];
				} else {
					for ( $i = 1; $i <= count( $chunks ); $i++ ) {
						$filename           = "$postType-$sitemapName$i.xml";
						$files[ $filename ] = $chunks[ $i - 1 ];
					}
				}
			}
		}

		$taxonomies = aioseo()->sitemap->helpers->includedTaxonomies();
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				aioseo()->sitemap->indexName = $taxonomy;

				$terms = aioseo()->sitemap->content->terms( $taxonomy );
				if ( ! $terms ) {
					continue;
				}

				$chunks = aioseo()->sitemap->helpers->chunkEntries( $terms );
				if ( 1 === count( $chunks ) ) {
					$filename           = "$taxonomy-$sitemapName.xml";
					$files[ $filename ] = $chunks[0];
				} else {
					for ( $i = 1; $i <= count( $chunks ); $i++ ) {
						$filename           = "$taxonomy-$sitemapName$i.xml";
						$files[ $filename ] = $chunks[ $i - 1 ];
					}
				}
			}
		}
		$this->writeSitemaps( $files );
	}

	/**
	 * Writes all sitemap files.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $files The sitemap files.
	 * @return void
	 */
	public function writeSitemaps( $files ) {
		$sitemapName = aioseo()->sitemap->helpers->filename();
		if ( aioseo()->sitemap->indexes ) {
			$indexes = [];
			foreach ( $files as $filename => $entries ) {
				if ( empty( $entries ) ) {
					continue;
				}
				$indexes[] = [
					'loc'     => trailingslashit( home_url() ) . $filename,
					'lastmod' => array_values( $entries )[0]['lastmod']
				];
			}
			$files[ "$sitemapName.xml" ] = $indexes;
			foreach ( $files as $filename => $entries ) {
				$this->writeSitemap( $filename, $entries );
			}
			return;
		}

		$content = [];
		foreach ( $files as $filename => $entries ) {
			foreach ( $entries as $entry ) {
				$content[] = $entry;
			}
		}
		$this->writeSitemap( "$sitemapName.xml", $content );
	}

	/**
	 * Writes a given sitemap file to the root dir.
	 *
	 * Helper function for writeSitemaps().
	 *
	 * @since 4.0.0
	 *
	 * @param  string $filename The name of the file.
	 * @param  array  $entries  The sitemap entries for the file.
	 * @return void
	 */
	protected function writeSitemap( $filename, $entries ) {
		$sitemapName                 = aioseo()->sitemap->helpers->filename();
		aioseo()->sitemap->indexName = $filename;
		if ( "$sitemapName.xml" === $filename && aioseo()->sitemap->indexes ) {
			// Set index name to root so that we use the right output template.
			aioseo()->sitemap->indexName = 'root';
		}

		ob_start();
		aioseo()->sitemap->output->output( $entries );
		foreach ( aioseo()->sitemap->addons as $addon => $classes ) {
			if ( ! empty( $classes['output'] ) ) {
				$classes['output']->output( $entries );
			}
		}
		$content = ob_get_clean();

		$file = ABSPATH . sanitize_file_name( $filename );
		$wpfs = aioseo()->helpers->wpfs();
		if ( $wpfs ) {
			$fileExists = @$wpfs->exists( $file );
			if ( ! $fileExists || @$wpfs->is_writable( $file ) ) {
				@$wpfs->put_contents( $file, $content );
			}
		}
	}

	/**
	 * Return an array of sitemap files.
	 *
	 * @since 4.0.0
	 *
	 * @return array An array of files.
	 */
	public function files() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$files = list_files( get_home_path(), 1 );
		if ( ! count( $files ) ) {
			return [];
		}

		$sitemapFiles = [];
		foreach ( $files as $index => $filename ) {
			if ( preg_match( '#.*sitemap.*#', $filename ) ) {
				$sitemapFiles[] = $filename;
			}
		}

		return $sitemapFiles;
	}
}