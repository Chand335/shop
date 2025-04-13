<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\Pages\ViewPost;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Resources\ViewRecord\Concerns\Translatable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;

class PostResource extends Resource
{
  protected static ?string $model = Post::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('title')
          ->required()
          ->live(onBlur: true)
          ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

        TextInput::make('slug')
          ->required()
          ->unique(Post::class, 'slug', ignoreRecord: true),

        Select::make('category_id')
          ->relationship('category', 'name')
          ->searchable()
          ->nullable(),
        Hidden::make('created_by')
          ->default(auth()->id()),
        // Select::make('created_by')
        //   ->relationship('author', 'name')
        //   ->default(auth()->id())
        //   ->disabled(),
        Select::make('tags')
          ->multiple()
          ->relationship('tags', 'name')
          ->preload()
          ->searchable()
          ->label('Tags'),

        Textarea::make('content')->required()->columnSpanFull(),

        FileUpload::make('image')->image()->directory('posts/images')->nullable(),

        DatePicker::make('published_at')->label('Publish Date'),

        Select::make('status')
          ->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
          ])
          ->default('draft'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('image'),
        TextColumn::make('title')->searchable()->description(fn(Post $record): string => $record->slug)->wrap(),
        TextColumn::make('status')
          ->badge()
          ->formatStateUsing(fn(string $state) => ucfirst($state))
          ->color(fn(string $state): string => match ($state) {
            'draft' => 'gray',
            'reviewing' => 'warning',
            'published' => 'success',
            'rejected' => 'danger',
            'archived' => 'secondary',
          }),
        // TextColumn::make('category.name')->label('Category'),
        TextColumn::make('author.name')
          ->formatStateUsing(fn(string $state) => ucfirst($state))->label('Auther'),
        TextColumn::make('published_at')->date()->label('Published Date'),
      ])
      ->filters([
        Filter::make('published_at')
          ->form([
            DatePicker::make('published_from')->label('Published from'),
            DatePicker::make('published_until')->label('Published until'),
          ])
          ->query(function ($query, array $data) {
            return $query
              ->when($data['published_from'], fn($q, $date) => $q->whereDate('published_at', '>=', $date))
              ->when($data['published_until'], fn($q, $date) => $q->whereDate('published_at', '<=', $date));
          }),
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->emptyStateActions([
        Action::make('create')
          ->label('Create post')
          ->url(route('filament.admin.resources.posts.create'))
          ->icon('heroicon-m-plus')
          ->button(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])->defaultSort('published_at', 'desc');
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPosts::route('/'),
      'create' => Pages\CreatePost::route('/create'),
      'view' => Pages\ViewPost::route('/{record}'),
      'edit' => Pages\EditPost::route('/{record}/edit'),
    ];
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Tabs::make('Tabs')
          ->tabs([
            Tabs\Tab::make('View Post')
              ->icon('heroicon-o-eye')
              ->schema([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('published_at')->badge()->date('M d, Y'),
                TextEntry::make('auther.name'),
                TextEntry::make('category.name'),
                TextEntry::make('tags')->badge()
                ->separator(','),
              ]),
            Tabs\Tab::make('Edit Post')
              ->icon('heroicon-o-eye')
              ->schema([
                // ...
              ]),
            Tabs\Tab::make('Manage Comments')
              ->icon('heroicon-o-eye')
              ->schema([
                // ...
              ]),
          ])

      ]);
  }
}
