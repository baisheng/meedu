<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) XiaoTeng <616896861@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http\Controllers\Backend\Api\V1;

use App\Models\Video;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\CourseVideoRequest;

class CourseVideoController extends BaseController
{
    public function index(Request $request)
    {
        $keywords = $request->input('keywords', '');

        $videos = Video::with(['course'], ['chapter'])
            ->when($keywords, function ($query) use ($keywords) {
                return $query->where('title', 'like', "{$keywords}%");
            })
            ->orderByDesc('published_at')
            ->paginate($request->input('size', 12));

        $videos->appends($request->input());

        return $this->successData($videos);
    }

    public function createParams()
    {
        $courses = Course::select(['id', 'title'])->orderByDesc('published_at')->get();

        return $this->successData(compact('courses'));
    }

    public function store(CourseVideoRequest $request, Video $video)
    {
        $video->fill($request->filldata())->save();

        return $this->success();
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);

        $courses = Course::all();

        return $this->successData(compact('video', 'courses'));
    }

    public function update(CourseVideoRequest $request, $id)
    {
        $video = Video::findOrFail($id);
        $video->fill($request->filldata())->save();

        return $this->success();
    }

    public function destroy($id)
    {
        Video::destroy($id);

        return $this->success();
    }
}
